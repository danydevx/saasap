<?php

namespace Modules\AiChatbot\Services;

use Modules\AiChatbot\Models\BusinessAiSetting;
use Modules\AiChatbot\Models\AiConversation;
use Modules\AiChatbot\Models\AiMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiChatbotService
{
    private BusinessAiSetting $settings;
    private VectorStoreService $vectorStore;

    public function __construct(BusinessAiSetting $settings)
    {
        $this->settings = $settings;
        $this->vectorStore = new VectorStoreService($settings);
    }

    public function chat(string $userMessage, string $sessionId, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        if (!$this->settings->is_enabled) {
            return [
                'success' => false,
                'error' => 'Chatbot is disabled',
                'message' => null,
            ];
        }

        if (!$this->settings->api_key) {
            return [
                'success' => false,
                'error' => 'API key not configured',
                'message' => null,
            ];
        }

        if (!$this->checkLimits($sessionId)) {
            return [
                'success' => false,
                'error' => 'Monthly limit reached',
                'message' => 'Has alcanzado el límite de conversaciones para este mes. Por favor, intenta más tarde.',
            ];
        }

        $conversation = $this->getOrCreateConversation($sessionId, $ipAddress, $userAgent);

        if ($conversation->messages_count >= $this->settings->max_messages_conversation) {
            return [
                'success' => false,
                'error' => 'Conversation limit reached',
                'message' => 'Has alcanzado el límite de mensajes en esta conversación. Inicia una nueva conversación.',
            ];
        }

        $contextChunks = $this->vectorStore->searchSimilar($userMessage, 5, 0.3);

        $conversationHistory = $this->getConversationHistory($conversation, 10);

        $systemPrompt = $this->buildSystemPrompt($contextChunks);

        $messages = $this->buildMessages($systemPrompt, $conversationHistory, $userMessage);

        try {
            $response = $this->callOpenAI($messages);
        } catch (\Exception $e) {
            Log::error('AI Chat error', [
                'business_id' => $this->settings->business_id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => 'AI service error',
                'message' => 'Disculpa, estoy teniendo problemas para responder. Por favor, intenta de nuevo.',
            ];
        }

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
            'tokens_used' => $this->estimateTokens($userMessage),
        ]);

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $response['content'],
            'tokens_used' => $response['tokens'] ?? 0,
        ]);

        $conversation->incrementMessagesCount();

        return [
            'success' => true,
            'error' => null,
            'message' => $response['content'],
            'tokens' => $response['tokens'] ?? 0,
        ];
    }

    public function checkLimits(string $sessionId): bool
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $conversationsThisMonth = AiConversation::where('business_id', $this->settings->business_id)
            ->where('started_at', '>=', $startOfMonth)
            ->count();

        return $conversationsThisMonth < $this->settings->max_conversations_month;
    }

    public function getConversationHistory(AiConversation $conversation, int $limit = 50): array
    {
        return $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();
    }

    public function getSettings(): BusinessAiSetting
    {
        return $this->settings;
    }

    public function reindexContent(): array
    {
        return $this->vectorStore->reindexBusiness();
    }

    private function getOrCreateConversation(string $sessionId, ?string $ipAddress, ?string $userAgent): AiConversation
    {
        $conversation = AiConversation::where('business_id', $this->settings->business_id)
            ->where('session_id', $sessionId)
            ->first();

        if (!$conversation) {
            $conversation = AiConversation::create([
                'business_id' => $this->settings->business_id,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'messages_count' => 0,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);
        }

        return $conversation;
    }

    private function buildSystemPrompt(array $contextChunks): string
    {
        $business = $this->settings->business;

        $systemPrompt = $this->settings->system_prompt ?: $this->settings->getDefaultSystemPrompt();

        $systemPrompt = str_replace('{business_name}', $business->name ?? 'este negocio', $systemPrompt);

        if (!empty($contextChunks)) {
            $contextText = "\n\nInformación de referencia:\n";
            foreach ($contextChunks as $chunk) {
                $contextText .= "- [{$chunk['source_type']}] {$chunk['chunk_text']}\n";
            }
            $systemPrompt .= $contextText;
        }

        return $systemPrompt;
    }

    private function buildMessages(string $systemPrompt, array $history, string $userMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    private function callOpenAI(array $messages): array
    {
        $response = Http::withToken($this->settings->api_key)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->settings->model,
                'messages' => $messages,
                'max_tokens' => $this->settings->max_tokens_response,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'tokens' => $data['usage']['total_tokens'] ?? 0,
        ];
    }

    private function estimateTokens(string $text): int
    {
        return (int) (strlen($text) / 4);
    }
}

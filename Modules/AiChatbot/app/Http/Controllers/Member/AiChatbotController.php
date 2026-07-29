<?php

namespace Modules\AiChatbot\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\AiChatbot\Models\BusinessAiSetting;
use Modules\AiChatbot\Models\AiContext;
use Modules\AiChatbot\Services\AiChatbotService;

class AiChatbotController extends Controller
{
    public function index(Request $request, \Modules\Businesses\Models\Business $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $settings = BusinessAiSetting::where('business_id', $business->id)->first();

        $contexts = AiContext::where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'content', 'is_active', 'created_at']);

        $embeddingCounts = [];
        if ($settings && $settings->is_enabled) {
            $vectorStore = new \Modules\AiChatbot\Services\VectorStoreService($settings);
            $counts = [
                'product' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'product')->count(),
                'service' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'service')->count(),
                'promotion' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'promotion')->count(),
                'faq' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'faq')->count(),
                'location' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'location')->count(),
                'about' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'about')->count(),
                'custom' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'custom')->count(),
                'restaurant_category' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'restaurant_category')->count(),
                'restaurant_product' => \Modules\AiChatbot\Models\AiEmbedding::where('business_id', $business->id)->where('source_type', 'restaurant_product')->count(),
            ];
            $embeddingCounts = $counts;
        }

        return Inertia::render('Member/AiChatbot/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'settings' => $settings ? [
                'id' => $settings->id,
                'provider' => $settings->provider,
                'api_key' => $settings->api_key ? '********' : '',
                'model' => $settings->model,
                'embedding_model' => $settings->embedding_model,
                'system_prompt' => $settings->system_prompt,
                'max_conversations_month' => $settings->max_conversations_month,
                'max_messages_conversation' => $settings->max_messages_conversation,
                'max_tokens_response' => $settings->max_tokens_response,
                'widget_color' => $settings->widget_color,
                'widget_theme' => $settings->widget_theme ?? 'light',
                'is_enabled' => $settings->is_enabled,
                'allow_reset_chat' => $settings->allow_reset_chat ?? false,
            ] : null,
            'contexts' => $contexts,
            'embeddingCounts' => $embeddingCounts,
            'businessMenu' => $request->attributes->get('businessMenu', []),
        ]);
    }

    public function saveSettings(Request $request, \Modules\Businesses\Models\Business $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'provider' => 'required|in:openai,minimax',
            'api_key' => 'required|string|max:500',
            'model' => 'required|string|max:100',
            'embedding_model' => 'required|string|max:100',
            'system_prompt' => 'nullable|string',
            'max_conversations_month' => 'required|integer|min:1|max:10000',
            'max_messages_conversation' => 'required|integer|min:1|max:500',
            'max_tokens_response' => 'required|integer|min:100|max:4000',
            'widget_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'widget_theme' => 'required|in:light,dark',
            'is_enabled' => 'boolean',
            'allow_reset_chat' => 'boolean',
        ]);

        $settings = BusinessAiSetting::updateOrCreate(
            ['business_id' => $business->id],
            [
                'provider' => $data['provider'],
                'api_key' => $data['api_key'],
                'model' => $data['model'],
                'embedding_model' => $data['embedding_model'],
                'system_prompt' => $data['system_prompt'] ?? null,
                'max_conversations_month' => $data['max_conversations_month'],
                'max_messages_conversation' => $data['max_messages_conversation'],
                'max_tokens_response' => $data['max_tokens_response'],
                'widget_color' => $data['widget_color'],
                'widget_theme' => $data['widget_theme'],
                'is_enabled' => $data['is_enabled'] ?? false,
                'allow_reset_chat' => $data['allow_reset_chat'] ?? false,
            ]
        );

        return redirect()->back()->with('success', 'Configuración guardada correctamente.');
    }

    public function storeContext(Request $request, \Modules\Businesses\Models\Business $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $context = AiContext::create([
            'business_id' => $business->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Contexto creado correctamente.');
    }

    public function updateContext(Request $request, \Modules\Businesses\Models\Business $business, $contextId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $context = AiContext::where('business_id', $business->id)->findOrFail($contextId);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $context->update($data);

        return redirect()->back()->with('success', 'Contexto actualizado correctamente.');
    }

    public function destroyContext(Request $request, \Modules\Businesses\Models\Business $business, $contextId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $context = AiContext::where('business_id', $business->id)->findOrFail($contextId);

        $context->delete();

        return redirect()->back()->with('success', 'Contexto eliminado correctamente.');
    }

    public function reindex(Request $request, \Modules\Businesses\Models\Business $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $settings = BusinessAiSetting::where('business_id', $business->id)->first();

        if (!$settings) {
            return redirect()->back()->with('error', 'Primero configura los ajustes del chatbot.');
        }

        if (!$settings->api_key) {
            return redirect()->back()->with('error', 'Primero ingresa la API key.');
        }

        try {
            $chatbotService = new AiChatbotService($settings);
            $stats = $chatbotService->reindexContent();

            $message = "Contenido reindexado: ";
            $message .= "{$stats['products']} productos, ";
            $message .= "{$stats['services']} servicios, ";
            $message .= "{$stats['promotions']} promociones, ";
            $message .= "{$stats['faqs']} FAQs, ";
            $message .= "{$stats['locations']} ubicaciones, ";
            $message .= "{$stats['about']} acerca de, ";
            $message .= "{$stats['custom']} contextos personalizados.";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al reindexar: ' . $e->getMessage());
        }
    }
}

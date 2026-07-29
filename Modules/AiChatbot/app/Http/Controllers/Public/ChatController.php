<?php

namespace Modules\AiChatbot\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\AiChatbot\Models\BusinessAiSetting;
use Modules\AiChatbot\Services\AiChatbotService;

class ChatController extends Controller
{
    public function chat(Request $request, $slug)
    {
        $business = \Modules\Businesses\Models\Business::where('slug', $slug)->firstOrFail();

        $settings = BusinessAiSetting::where('business_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->json([
                'success' => false,
                'error' => 'Chatbot not available',
                'message' => null,
            ], 404);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $sessionId = $data['session_id'] ?? $request->session()->getId() ?? Str::uuid()->toString();

        try {
            $chatbotService = new AiChatbotService($settings);
            $result = $chatbotService->chat(
                $data['message'],
                $sessionId,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Service error',
                'message' => 'Disculpa, estoy teniendo problemas para responder. Por favor, intenta de nuevo.',
            ], 500);
        }
    }

    public function getSettings(Request $request, $slug)
    {
        $business = \Modules\Businesses\Models\Business::where('slug', $slug)->firstOrFail();

        $settings = BusinessAiSetting::where('business_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->json([
                'available' => false,
            ]);
        }

        return response()->json([
            'available' => true,
            'business_name' => $business->name,
            'widget_color' => $settings->widget_color,
            'widget_theme' => $settings->widget_theme ?? 'light',
        ]);
    }

    public function conversation(Request $request, $slug)
    {
        $business = \Modules\Businesses\Models\Business::where('slug', $slug)->firstOrFail();

        $settings = BusinessAiSetting::where('business_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->json([
                'success' => false,
                'messages' => [],
            ]);
        }

        $sessionId = $request->get('session_id') ?? $request->session()->getId();

        $conversation = \Modules\AiChatbot\Models\AiConversation::where('business_id', $business->id)
            ->where('session_id', $sessionId)
            ->with('messages')
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => true,
                'messages' => [],
            ]);
        }

        $messages = $conversation->messages->map(fn($msg) => [
            'role' => $msg->role,
            'content' => $msg->content,
            'created_at' => $msg->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}

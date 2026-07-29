<?php

namespace Modules\AiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class BusinessAiSetting extends Model
{
    protected $table = 'business_ai_settings';

    protected $fillable = [
        'business_id',
        'provider',
        'api_key',
        'model',
        'embedding_model',
        'system_prompt',
        'max_conversations_month',
        'max_messages_conversation',
        'max_tokens_response',
        'widget_color',
        'widget_theme',
        'is_enabled',
        'allow_reset_chat',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allow_reset_chat' => 'boolean',
        'max_conversations_month' => 'integer',
        'max_messages_conversation' => 'integer',
        'max_tokens_response' => 'integer',
    ];

    protected $hidden = [
        'api_key',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function setApiKeyAttribute($value): void
    {
        $this->attributes['api_key'] = Crypt::encryptString($value);
    }

    public function getApiKeyAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDefaultSystemPrompt(): string
    {
        return "Eres un asistente virtual amigable y útil de {business_name}. Tu objetivo es ayudar a los clientes con información sobre productos, servicios, promociones y cualquier consulta relacionada con el negocio. Responde de manera clara, concisa y en español. Si no tienes información suficiente, indica que no estás seguro y sugiere contactar directamente al negocio.";
    }
}

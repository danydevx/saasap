<?php

namespace Modules\AiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotPreset extends Model
{
    protected $table = 'chatbot_presets';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'business_type',
        'system_prompt_template',
        'chatbot_name_template',
        'greeting_message',
        'fallback_message',
        'personality',
        'language',
        'configuration',
        'initial_suggestions',
        'is_active',
        'is_system',
        'created_by',
    ];

    protected $casts = [
        'configuration' => 'array',
        'initial_suggestions' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->configuration[$key] ?? $default;
    }

    public static function getActivePresets()
    {
        return self::where('is_active', true)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }

    public static function getForBusinessType(?string $type)
    {
        return self::where('is_active', true)
            ->where(function ($q) use ($type) {
                $q->whereNull('business_type')
                  ->orWhere('business_type', $type);
            })
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = self::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        return $query->exists();
    }
}

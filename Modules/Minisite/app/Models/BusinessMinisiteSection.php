<?php

namespace Modules\Minisite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMinisiteSection extends Model
{
    protected $fillable = [
        'business_id',
        'section_type',
        'section_key',
        'title',
        'description',
        'config',
        'buttons',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'buttons' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public static function getSectionTypes(): array
    {
        return [
            'services' => 'Servicios',
            'gallery' => 'Galería',
            'promotions' => 'Promociones',
            'contact_form' => 'Formulario de Contacto',
        ];
    }

    public static function getDefaultConfig(string $type): array
    {
        return match ($type) {
            'services' => [
                'view_mode' => 'carousel',
                'show_image' => true,
                'show_price' => true,
                'show_description' => false,
                'service_ids' => [],
            ],
            'gallery' => [
                'gallery_id' => null,
                'images_limit' => 10,
            ],
            'promotions' => [
                'show_all' => true,
                'promotion_ids' => [],
            ],
            'contact_form' => [
                'form_id' => null,
            ],
            default => [],
        };
    }
}

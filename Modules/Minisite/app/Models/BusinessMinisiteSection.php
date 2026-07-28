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
            'hero' => 'Encabezado (Hero)',
            'services' => 'Servicios',
            'gallery' => 'Galería',
            'promotions' => 'Promociones',
            'contact_form' => 'Formulario de Contacto',
            'locations' => 'Ubicaciones',
            'about' => 'Nosotros',
            'features' => 'Características',
            'faqs' => 'Preguntas Frecuentes',
            'products' => 'Productos',
            'footer' => 'Pie de Página (Footer)',
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
            'locations' => [
                'show_all' => true,
                'location_ids' => [],
                'show_address' => true,
                'show_phone' => true,
                'show_email' => true,
                'show_hours' => true,
            ],
            'about' => [
                'show_image' => true,
                'show_description' => true,
            ],
            'features' => [
                'show_all' => true,
                'feature_ids' => [],
                'show_icon' => true,
                'show_title' => true,
                'show_description' => true,
            ],
            'faqs' => [
                'show_all' => true,
                'faq_ids' => [],
                'category_id' => null,
                'show_questions' => true,
            ],
            'products' => [
                'show_all' => true,
                'product_ids' => [],
                'show_image' => true,
                'show_price' => true,
                'show_compare_price' => true,
                'show_add_to_cart' => false,
                'view_mode' => 'grid',
            ],
            'hero' => [
                'show' => true,
            ],
            'footer' => [
                'show' => true,
            ],
            default => [],
        };
    }
}

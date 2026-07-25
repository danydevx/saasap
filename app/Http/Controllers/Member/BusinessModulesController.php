<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Businesses\Models\Business;
use Modules\Gallery\Models\BusinessGalleryImage;
use Modules\Leads\Models\BusinessLead;
use Modules\Locations\Models\BusinessLocation;
use Modules\Products\Models\BusinessProduct;
use Modules\Promotions\Models\BusinessPromotion;
use Modules\Reviews\Models\BusinessReview;
use Modules\Services\Models\BusinessService;
use Modules\Faqs\Models\BusinessFaq;
use Modules\SocialMedia\Models\BusinessSocialNetwork;

class BusinessModulesController extends Controller
{
    public function show(Request $request, Business $business)
    {
        $user = $request->user();
        abort_unless($user->id === $business->user_id || $user->hasAnyRole(['superadmin', 'admin']), 403);

        $bizId = $business->id;

        $moduleSummary = [
            [
                'key' => 'locations',
                'name' => 'Ubicaciones',
                'description' => 'Gestiona múltiples ubicaciones o direcciones para tu negocio',
                'icon' => 'bi bi-geo-alt',
                'count' => BusinessLocation::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/locations",
            ],
            [
                'key' => 'gallery',
                'name' => 'Galería',
                'description' => 'Galería de imágenes para mostrar tus productos o servicios',
                'icon' => 'bi bi-images',
                'count' => BusinessGalleryImage::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/galleries",
            ],
            [
                'key' => 'leads',
                'name' => 'Leads',
                'description' => 'Captura y gestión de prospectos de clientes',
                'icon' => 'bi bi-person-plus',
                'count' => BusinessLead::where('business_id', $bizId)->count(),
                'url' => "/member/leads?business={$bizId}",
            ],
            [
                'key' => 'services',
                'name' => 'Servicios',
                'description' => 'Gestiona los servicios que ofreces',
                'icon' => 'bi bi-briefcase',
                'count' => BusinessService::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/services",
            ],
            [
                'key' => 'appointments',
                'name' => 'Citas y Reservas',
                'description' => 'Sistema de citas y reservas en línea',
                'icon' => 'bi bi-calendar-check',
                'count' => BusinessAppointment::where('business_id', $bizId)->count(),
                'url' => "/member/appointments?business={$bizId}",
            ],
            [
                'key' => 'products',
                'name' => 'Productos',
                'description' => 'Gestión de productos en venta',
                'icon' => 'bi bi-box-seam',
                'count' => BusinessProduct::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/products",
            ],
            [
                'key' => 'reviews',
                'name' => 'Reviews',
                'description' => 'Customer reviews and testimonials',
                'icon' => 'bi bi-star',
                'count' => BusinessReview::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/reviews",
            ],
            [
                'key' => 'promotions',
                'name' => 'Promociones',
                'description' => 'Manage promotions, deals and coupons',
                'icon' => 'bi bi-tag',
                'count' => BusinessPromotion::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/promotions",
            ],
            [
                'key' => 'faqs',
                'name' => 'Preguntas Frecuentes',
                'description' => 'Gestiona las preguntas frecuentes de tu negocio',
                'icon' => 'bi bi-question-circle',
                'count' => BusinessFaq::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/faqs",
            ],
            [
                'key' => 'socialmedia',
                'name' => 'Redes Sociales',
                'description' => 'Gestiona tus redes sociales',
                'icon' => 'bi bi-share',
                'count' => BusinessSocialNetwork::where('business_id', $bizId)->count(),
                'url' => "/member/businesses/{$bizId}/social-networks",
            ],
        ];

        return Inertia::render('Member/BusinessModules', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'moduleSummary' => $moduleSummary,
        ]);
    }
}

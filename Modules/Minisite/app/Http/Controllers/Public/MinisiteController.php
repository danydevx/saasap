<?php

namespace Modules\Minisite\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Minisite\Models\BusinessMinisiteSection;
use Modules\Minisite\Models\BusinessMinisiteSetting;

class MinisiteController extends Controller
{
    public function show(string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$business) {
            abort(404);
        }

        $setting = BusinessMinisiteSetting::where('business_id', $business->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $sections = BusinessMinisiteSection::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($section) use ($business) {
                $config = $section->config ?? [];

                $sectionData = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'section_key' => $section->section_key,
                    'title' => $section->title,
                    'config' => $config,
                ];

                switch ($section->section_type) {
                    case 'services':
                        $sectionData['items'] = $this->getServicesData($business, $config);
                        break;
                    case 'gallery':
                        $sectionData['items'] = $this->getGalleryData($business, $config);
                        break;
                    case 'promotions':
                        $sectionData['items'] = $this->getPromotionsData($business, $config);
                        break;
                    case 'contact_form':
                        $sectionData['form'] = $this->getContactFormData($business, $config);
                        break;
                }

                return $sectionData;
            });

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        return Inertia::render('Minisite/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'sections' => $sections,
            'socialNetworks' => $socialNetworks,
        ]);
    }

    private function getServicesData(Business $business, array $config): array
    {
        $query = $business->services()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['service_ids'])) {
            $query->whereIn('id', $config['service_ids']);
        }

        if (empty($config['service_ids'])) {
            $limit = 20;
            $query->limit($limit);
        }

        return $query->get(['id', 'name', 'description', 'price', 'image'])->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'image' => $service->image,
            ];
        })->toArray();
    }

    private function getGalleryData(Business $business, array $config): array
    {
        $galleryId = $config['gallery_id'] ?? null;
        $limit = $config['images_limit'] ?? 10;

        $query = $business->galleryImages()
            ->where('is_active', true);

        if ($galleryId) {
            $query->where('business_gallery_id', $galleryId);
        }

        return $query
            ->orderBy('sort_order')
            ->limit($limit)
            ->get(['id', 'path', 'title', 'description'])
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'title' => $image->title,
                    'description' => $image->description,
                ];
            })->toArray();
    }

    private function getPromotionsData(Business $business, array $config): array
    {
        $query = $business->promotions()
            ->where('is_active', true);

        if (!empty($config['promotion_ids'])) {
            $query->whereIn('id', $config['promotion_ids']);
        }

        return $query
            ->orderBy('sort_order')
            ->get(['id', 'name', 'description', 'promotion_price', 'expires_at', 'regular_price', 'coupon_code'])
            ->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'title' => $promo->name,
                    'description' => $promo->description,
                    'regular_price' => $promo->regular_price,
                    'promotion_price' => $promo->promotion_price,
                    'expires_at' => $promo->expires_at,
                    'coupon_code' => $promo->coupon_code,
                ];
            })->toArray();
    }

    private function getContactFormData(Business $business, array $config): ?array
    {
        $formId = $config['form_id'] ?? null;

        if (!$formId) {
            return null;
        }

        $form = $business->contactForms()
            ->where('is_active', true)
            ->find($formId);

        if (!$form) {
            return null;
        }

        $form->load('fields');

        return [
            'id' => $form->id,
            'shortcode' => $form->shortcode,
            'fields' => $form->fields->map(fn($f) => $f->getConfig())->toArray(),
        ];
    }
}

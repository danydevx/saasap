<?php

namespace Modules\Minisite\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Gallery\Models\BusinessGallery;
use Modules\Minisite\Models\BusinessMinisiteSection;
use Modules\Minisite\Models\BusinessMinisiteSetting;
use Modules\ContactForm\Models\BusinessContactForm;

class MinisiteSectionController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $sections = BusinessMinisiteSection::where('business_id', $business->id)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($section) use ($business) {
                $config = $section->config ?? [];
                $sectionData = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'section_key' => $section->section_key,
                    'title' => $section->title,
                    'description' => $section->description,
                    'config' => $config,
                    'buttons' => $section->buttons ?? [],
                    'sort_order' => $section->sort_order,
                    'is_active' => $section->is_active,
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

        $setting = BusinessMinisiteSetting::where('business_id', $business->id)->first();

        $socialNetworks = [];
        if (class_exists('\Modules\SocialMedia\Models\BusinessSocialNetwork')) {
            $socialNetworks = \Modules\SocialMedia\Models\BusinessSocialNetwork::where('business_id', $business->id)
                ->where('is_active', true)
                ->where('show_on_footer', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($sn) => [
                    'platform' => $sn->platform,
                    'url' => $sn->url,
                    'icon' => $sn->icon_class ?? 'bi bi-share',
                ])
                ->toArray();
        }

        return Inertia::render('Member/Minisite/Sections/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
            ],
            'sections' => $sections,
            'setting' => $setting ? [
                'is_active' => $setting->is_active,
                'hero_layout' => $setting->hero_layout ?? 'left',
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ] : [
                'is_active' => false,
                'hero_layout' => 'left',
                'hero_title' => '',
                'hero_subtitle' => '',
                'hero_background_image' => null,
                'footer_text' => '',
                'footer_show_social' => true,
            ],
            'socialNetworks' => $socialNetworks,
            'sectionTypes' => BusinessMinisiteSection::getSectionTypes(),
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $galleries = BusinessGallery::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $forms = BusinessContactForm::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Minisite/Sections/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'sectionTypes' => BusinessMinisiteSection::getSectionTypes(),
            'galleries' => $galleries,
            'forms' => $forms,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $data = $request->validate([
            'section_type' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'config' => ['nullable', 'array'],
            'buttons' => ['nullable', 'array'],
            'buttons.*.text' => ['required', 'string', 'max:50'],
            'buttons.*.url' => ['required', 'string', 'max:255'],
            'buttons.*.style' => ['nullable', 'string', 'in:primary,secondary,outline'],
            'is_active' => ['boolean'],
        ]);

        $typeCount = BusinessMinisiteSection::where('business_id', $business->id)
            ->where('section_type', $data['section_type'])
            ->count();

        $sectionKey = $data['section_type'] . '_' . ($typeCount + 1);

        $maxOrder = BusinessMinisiteSection::where('business_id', $business->id)->max('sort_order') ?? 0;

        $section = BusinessMinisiteSection::create([
            'business_id' => $business->id,
            'section_type' => $data['section_type'],
            'section_key' => $sectionKey,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'config' => $data['config'] ?? BusinessMinisiteSection::getDefaultConfig($data['section_type']),
            'buttons' => $data['buttons'] ?? [],
            'sort_order' => $maxOrder + 1,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('member.businesses.minisite.sections.index', $business->id)
            ->with('success', 'Sección creada.');
    }

    public function edit(Request $request, Business $business, BusinessMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->business_id === $business->id, 403);

        $galleries = BusinessGallery::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $forms = BusinessContactForm::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Minisite/Sections/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'section' => [
                'id' => $section->id,
                'section_type' => $section->section_type,
                'section_key' => $section->section_key,
                'title' => $section->title,
                'description' => $section->description,
                'config' => $section->config,
                'buttons' => $section->buttons ?? [],
                'sort_order' => $section->sort_order,
                'is_active' => $section->is_active,
            ],
            'sectionTypes' => BusinessMinisiteSection::getSectionTypes(),
            'galleries' => $galleries,
            'forms' => $forms,
        ]);
    }

    public function update(Request $request, Business $business, BusinessMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->business_id === $business->id, 403);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'config' => ['nullable', 'array'],
            'buttons' => ['nullable', 'array'],
            'buttons.*.text' => ['required', 'string', 'max:50'],
            'buttons.*.url' => ['required', 'string', 'max:255'],
            'buttons.*.style' => ['nullable', 'string', 'in:primary,secondary,outline'],
            'is_active' => ['boolean'],
        ]);

        $section->update($data);

        return redirect()->back()->with('success', 'Sección actualizada.');
    }

    public function destroy(Request $request, Business $business, BusinessMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->business_id === $business->id, 403);

        $section->delete();

        return redirect()->back()->with('success', 'Sección eliminada.');
    }

    public function reorder(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:business_minisite_sections,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            BusinessMinisiteSection::where('id', $id)
                ->where('business_id', $business->id)
                ->update(['sort_order' => $index + 1]);
        }

        return back(303);
    }

    private function getServicesData(Business $business, array $config): array
    {
        $query = $business->services()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['service_ids'])) {
            $query->whereIn('id', $config['service_ids']);
        }

        $limit = 20;
        $query->limit($limit);

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
            ->get(['id', 'name', 'description', 'regular_price', 'promotion_price', 'expires_at'])
            ->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'title' => $promo->name,
                    'description' => $promo->description,
                    'regular_price' => $promo->regular_price,
                    'promotion_price' => $promo->promotion_price,
                    'expires_at' => $promo->expires_at,
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

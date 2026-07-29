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
                    case 'appointments':
                        $sectionData['appointments'] = $this->getAppointmentsData($business, $config);
                        break;
                    case 'availability':
                        $sectionData['availability'] = $this->getAvailabilityData($business, $config);
                        break;
                    case 'locations':
                        $sectionData['items'] = $this->getLocationsData($business, $config);
                        break;
                    case 'about':
                        $sectionData['content'] = $this->getAboutData($business, $config);
                        break;
                    case 'features':
                        $sectionData['items'] = $this->getFeaturesData($business, $config);
                        break;
                    case 'faqs':
                        $sectionData['items'] = $this->getFaqsData($business, $config);
                        break;
                    case 'products':
                        $sectionData['items'] = $this->getProductsData($business, $config);
                        break;
                    case 'reviews':
                        $sectionData['items'] = $this->getReviewsData($business, $config);
                        break;
                    case 'restaurant_menu':
                        $sectionData['items'] = $this->getRestaurantMenuData($business, $config);
                        break;
                }

                return $sectionData;
            });

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($business);

        $aiChatbot = $this->getAiChatbotSettings($business);

        return Inertia::render('Minisite/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
        ]);
    }

    public function services(string $slug)
    {
        return $this->renderPage($slug, 'services', 'Servicios', fn($b) => ['items' => $this->getServicesData($b, [])]);
    }

    public function products(string $slug)
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

        $categories = $business->productCategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($cat) => ['id' => $cat->id, 'name' => $cat->name])
            ->toArray();

        $products = $this->getProductsData($business, []);

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($business);
        $aiChatbot = $this->getAiChatbotSettings($business);

        return Inertia::render('Minisite/Products', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'pageTitle' => 'Productos',
            'sectionData' => [
                'items' => $products,
                'categories' => $categories,
            ],
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
        ]);
    }

    public function menu(string $slug)
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

        $menuData = $this->getRestaurantMenuData($business, []);

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($business);
        $aiChatbot = $this->getAiChatbotSettings($business);

        return Inertia::render('Minisite/Menu', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'pageTitle' => 'Menú',
            'menuData' => $menuData,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
        ]);
    }

    public function productDetail(string $slug, string $productSlug)
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

        $product = $business->products()
            ->where('is_active', true)
            ->where('slug', $productSlug)
            ->with('images')
            ->first();

        if (!$product) {
            abort(404, 'Producto no encontrado');
        }

        $imagePath = $product->image;
        if (!$imagePath && $product->images && $product->images->isNotEmpty()) {
            $imagePath = $product->images->first()->path;
        }

        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $galleryImages = $product->images->map(function ($img) {
            $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
            return [
                'id' => $img->id,
                'path' => $path,
                'title' => $img->title ?? '',
            ];
        })->toArray();

        $relatedProducts = $business->products()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->limit(4)
            ->get()
            ->map(function ($p) {
                $img = $p->image;
                if (!$img && $p->images->isNotEmpty()) {
                    $img = $p->images->first()->path;
                }
                $imgPath = $img ? "/storage/{$img}" : null;
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'image' => $imgPath,
                ];
            })->toArray();

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($business);

        return Inertia::render('Minisite/ProductDetail', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'quantity' => $product->quantity,
                'whatsapp_contact' => $product->whatsapp_contact,
                'image' => $finalPath,
                'gallery' => $galleryImages,
            ],
            'relatedProducts' => $relatedProducts,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
        ]);
    }

    public function serviceDetail(string $slug, string $serviceSlug)
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

        $service = $business->services()
            ->where('is_active', true)
            ->where('slug', $serviceSlug)
            ->with('images')
            ->first();

        if (!$service) {
            abort(404, 'Servicio no encontrado');
        }

        $imagePath = $service->image;
        if (!$imagePath && $service->images && $service->images->isNotEmpty()) {
            $imagePath = $service->images->first()->path;
        }

        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $galleryImages = $service->images->map(function ($img) {
            $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
            return [
                'id' => $img->id,
                'path' => $path,
                'title' => $img->title ?? '',
            ];
        })->toArray();

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($business);

        return Inertia::render('Minisite/ServiceDetail', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'deposit_amount' => $service->deposit_amount,
                'deposit_required' => $service->deposit_required,
                'allows_online_booking' => $service->allows_online_booking,
                'whatsapp_contact' => $service->whatsapp_contact,
                'image' => $finalPath,
                'gallery' => $galleryImages,
            ],
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
        ]);
    }

    public function gallery(string $slug)
    {
        return $this->renderPage($slug, 'gallery', 'Galería', fn($b) => ['items' => $this->getGalleryData($b, [])]);
    }

    public function appointments(string $slug)
    {
        return $this->renderPage($slug, 'appointments', 'Citas y Reservas', fn($b) => ['appointments' => $this->getAppointmentsData($b, [])]);
    }

    public function promotions(string $slug)
    {
        return $this->renderPage($slug, 'promotions', 'Promociones', fn($b) => ['items' => $this->getPromotionsData($b, [])]);
    }

    public function promotionDetail(string $slug, string $promotionSlug)
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

        $promotion = $business->promotions()
            ->where('is_active', true)
            ->where('slug', $promotionSlug)
            ->first();

        if (!$promotion) {
            abort(404, 'Promocion no encontrada');
        }

        $imagePath = $promotion->image;
        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $relatedPromotions = $business->promotions()
            ->where('is_active', true)
            ->where('id', '!=', $promotion->id)
            ->limit(4)
            ->get()
            ->map(function ($p) {
                $imgPath = $p->image;
                if ($imgPath) {
                    if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
                        $finalImgPath = $imgPath;
                    } else {
                        $finalImgPath = "/storage/{$imgPath}";
                    }
                } else {
                    $finalImgPath = null;
                }
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'promotion_price' => $p->promotion_price,
                    'regular_price' => $p->regular_price,
                    'image' => $finalImgPath,
                ];
            })->toArray();

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($business);

        $discountPercent = null;
        if ($promotion->regular_price && $promotion->promotion_price && $promotion->regular_price > $promotion->promotion_price) {
            $discountPercent = round((1 - $promotion->promotion_price / $promotion->regular_price) * 100);
        }

        return Inertia::render('Minisite/PromotionDetail', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'promotion' => [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'slug' => $promotion->slug,
                'description' => $promotion->description,
                'regular_price' => $promotion->regular_price,
                'promotion_price' => $promotion->promotion_price,
                'discount_percent' => $discountPercent,
                'coupon_code' => $promotion->coupon_code,
                'qr_code_path' => $promotion->qr_code_path ? (
                    str_starts_with($promotion->qr_code_path, 'http://') || str_starts_with($promotion->qr_code_path, 'https://')
                        ? $promotion->qr_code_path
                        : "/storage/{$promotion->qr_code_path}"
                ) : null,
                'starts_at' => $promotion->starts_at,
                'expires_at' => $promotion->expires_at,
                'image' => $finalPath,
            ],
            'relatedPromotions' => $relatedPromotions,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
        ]);
    }

    public function locations(string $slug)
    {
        return $this->renderPage($slug, 'locations', 'Ubicaciones', fn($b) => ['items' => $this->getLocationsData($b, [])]);
    }

    public function reviews(string $slug)
    {
        return $this->renderPage($slug, 'reviews', 'Reseñas', fn($b) => ['items' => $this->getReviewsData($b, [])]);
    }

    public function faqs(string $slug)
    {
        return $this->renderPage($slug, 'faqs', 'Preguntas Frecuentes', fn($b) => ['items' => $this->getFaqsData($b, [])]);
    }

    public function contact(string $slug)
    {
        return $this->renderPage($slug, 'contact', 'Contacto', fn($b) => ['form' => $this->getContactFormData($b, [])]);
    }

    private function renderPage(string $slug, string $sectionType, string $pageTitle, callable $dataLoader): \Inertia\Response
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

        $existingSections = $this->getExistingSections($business);
        $pageData = $dataLoader($business);
        $aiChatbot = $this->getAiChatbotSettings($business);

        $socialNetworks = $business->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $pageKeyMap = [
            'services' => 'Minisite/Services',
            'products' => 'Minisite/Products',
            'gallery' => 'Minisite/Gallery',
            'appointments' => 'Minisite/Appointments',
            'promotions' => 'Minisite/Promotions',
            'locations' => 'Minisite/Locations',
            'reviews' => 'Minisite/Reviews',
            'faqs' => 'Minisite/Faqs',
            'contact' => 'Minisite/Contact',
        ];

        return Inertia::render($pageKeyMap[$sectionType], [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
                'cover_image' => $business->cover_image_path,
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
            'pageTitle' => $pageTitle,
            'sectionData' => $pageData,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
        ]);
    }

    private function getExistingSections(Business $business): array
    {
        $sections = [];

        if ($business->services()->where('is_active', true)->exists()) {
            $sections[] = 'services';
        }
        if ($business->products()->where('is_active', true)->exists()) {
            $sections[] = 'products';
        }
        if ($business->galleryImages()->where('is_active', true)->exists()) {
            $sections[] = 'gallery';
        }
        if ($business->appointments()->exists()) {
            $sections[] = 'appointments';
        }
        if ($business->availability()->exists()) {
            $sections[] = 'availability';
        }
        if ($business->promotions()->where('is_active', true)->exists()) {
            $sections[] = 'promotions';
        }
        if ($business->locations()->where('is_active', true)->exists()) {
            $sections[] = 'locations';
        }
        if ($business->reviews()->where('is_active', true)->exists()) {
            $sections[] = 'reviews';
        }
        if ($business->faqs()->where('is_active', true)->exists()) {
            $sections[] = 'faqs';
        }
        if ($business->contactForms()->where('is_active', true)->exists()) {
            $sections[] = 'contact_form';
        }
        if (\Modules\RestaurantMenu\Entities\MenuCategory::where('business_id', $business->id)->where('active', true)->has('activeProducts')->exists()) {
            $sections[] = 'restaurant_menu';
        }

        return $sections;
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

        return $query
            ->with('images')
            ->get(['id', 'name', 'slug', 'description', 'price', 'duration_minutes', 'deposit_amount', 'deposit_required', 'allows_online_booking', 'whatsapp_contact', 'image'])
            ->map(function ($service) {
                $imagePath = $service->image;
                if (!$imagePath && $service->images && $service->images->isNotEmpty()) {
                    $imagePath = $service->images->first()->path;
                }

                if ($imagePath) {
                    if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                        $finalPath = $imagePath;
                    } else {
                        $finalPath = "/storage/{$imagePath}";
                    }
                } else {
                    $finalPath = null;
                }

                $galleryImages = $service->images->map(function ($img) {
                    $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
                    return [
                        'id' => $img->id,
                        'path' => $path,
                        'title' => $img->title ?? '',
                    ];
                })->toArray();

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'price' => $service->price,
                    'duration_minutes' => $service->duration_minutes,
                    'deposit_amount' => $service->deposit_amount,
                    'deposit_required' => $service->deposit_required,
                    'allows_online_booking' => $service->allows_online_booking,
                    'whatsapp_contact' => $service->whatsapp_contact,
                    'image' => $finalPath,
                    'gallery' => $galleryImages,
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
            ->get(['id', 'name', 'slug', 'description', 'promotion_price', 'expires_at', 'regular_price', 'coupon_code'])
            ->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'slug' => $promo->slug,
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

    private function getAppointmentsData(Business $business, array $config): array
    {
        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'duration_minutes' => $service->duration_minutes,
                    'price' => $service->price,
                ];
            })->toArray();

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city'])
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address_line_1,
                    'city' => $location->city,
                ];
            })->toArray();

        $availableDays = $business->availability()
            ->where('is_available', true)
            ->pluck('day_of_week')
            ->toArray();

        return [
            'services' => $services,
            'locations' => $locations,
            'availableDays' => $availableDays,
        ];
    }

    private function getAvailabilityData(Business $business, array $config): array
    {
        $schedule = $business->availability()
            ->orderBy('day_of_week')
            ->get(['day_of_week', 'is_available', 'start_time', 'end_time', 'slot_duration_minutes'])
            ->map(function ($day) {
                return [
                    'day_of_week' => $day->day_of_week,
                    'day_name' => \Modules\Appointments\Models\BusinessAvailability::dayShortName($day->day_of_week),
                    'is_available' => $day->is_available,
                    'start_time' => $day->start_time,
                    'end_time' => $day->end_time,
                    'slot_duration_minutes' => $day->slot_duration_minutes,
                ];
            })
            ->toArray();

        $exceptions = $business->availabilityExceptions()
            ->orderBy('exception_date')
            ->get(['exception_date', 'is_available', 'start_time', 'end_time', 'reason'])
            ->map(function ($exc) {
                return [
                    'exception_date' => $exc->exception_date->format('Y-m-d'),
                    'is_available' => $exc->is_available,
                    'start_time' => $exc->start_time,
                    'end_time' => $exc->end_time,
                    'reason' => $exc->reason,
                ];
            })
            ->toArray();

        return [
            'schedule' => $schedule,
            'exceptions' => $exceptions,
        ];
    }

    private function getRestaurantMenuData(Business $business, array $config): array
    {
        $query = \Modules\RestaurantMenu\Entities\MenuCategory::where('business_id', $business->id)
            ->where('active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('active', true)->orderBy('sort_order');
            }, 'activeProducts', 'children.activeProducts']);

        if (!empty($config['category_ids'])) {
            $query->whereIn('id', $config['category_ids']);
        }

        $categories = $query->orderBy('sort_order')->get();

        return $categories->map(function ($category) use ($config) {
            $products = $category->activeProducts->map(function ($product) use ($config) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->display_price,
                    'base_price' => $product->base_price,
                    'has_variants' => $product->activeVariants->count() > 0,
                    'variants' => $product->activeVariants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'title' => $variant->title,
                            'description' => $variant->description,
                            'price' => $variant->display_price,
                        ];
                    })->toArray(),
                    'image' => $product->image,
                    'gallery' => $product->images->map(fn($img) => ['id' => $img->id, 'path' => $img->path, 'title' => $img->title])->toArray(),
                ];
            })->toArray();

            $children = $category->children->map(function ($child) use ($config) {
                $childProducts = $child->activeProducts->map(function ($product) use ($config) {
                    return [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'price' => $product->display_price,
                        'base_price' => $product->base_price,
                        'has_variants' => $product->activeVariants->count() > 0,
                        'variants' => $product->activeVariants->map(function ($variant) {
                            return [
                                'id' => $variant->id,
                                'title' => $variant->title,
                                'description' => $variant->description,
                                'price' => $variant->display_price,
                            ];
                        })->toArray(),
                        'image' => $product->image,
                        'gallery' => $product->images->map(fn($img) => ['id' => $img->id, 'path' => $img->path, 'title' => $img->title])->toArray(),
                    ];
                })->toArray();

                return [
                    'id' => $child->id,
                    'title' => $child->title,
                    'products' => $childProducts,
                ];
            })->toArray();

            return [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description,
                'products' => $products,
                'children' => $children,
            ];
        })->toArray();
    }

    private function getLocationsData(Business $business, array $config): array
    {
        $query = $business->locations()
            ->where('is_active', true);

        if (!empty($config['location_ids'])) {
            $query->whereIn('id', $config['location_ids']);
        }

        return $query
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city', 'state', 'state_code', 'country', 'phone', 'email', 'latitude', 'longitude', 'directions_url'])
            ->map(function ($location) {
                $statePart = $location->state ?: $location->state_code;
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address_line_1,
                    'city' => $location->city,
                    'state' => $statePart,
                    'country' => $location->country,
                    'full_address' => trim("{$location->address_line_1}, {$location->city}, {$statePart}"),
                    'phone' => $location->phone,
                    'email' => $location->email,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'directions_url' => $location->directions_url,
                ];
            })->toArray();
    }

    private function getAboutData(Business $business, array $config): array
    {
        return [
            'name' => $business->name,
            'description' => $business->description ?? '',
            'logo' => $business->logo,
            'image' => $business->image,
        ];
    }

    private function getFeaturesData(Business $business, array $config): array
    {
        $query = $business->features()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['feature_ids'])) {
            $query->whereIn('id', $config['feature_ids']);
        }

        return $query
            ->get(['id', 'title', 'description', 'icon'])
            ->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'title' => $feature->title,
                    'description' => $feature->description,
                    'icon' => $feature->icon ?? 'bi bi-check-circle',
                ];
            })->toArray();
    }

    private function getFaqsData(Business $business, array $config): array
    {
        $query = $business->faqs()
            ->where('is_active', true)
            ->whereNull('category_id');

        if (!empty($config['faq_ids'])) {
            $query->whereIn('id', $config['faq_ids']);
        }

        if (!empty($config['category_id'])) {
            $query->where('category_id', $config['category_id']);
        }

        return $query
            ->orderBy('sort_order')
            ->get(['id', 'question', 'answer', 'category_id'])
            ->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category_id' => $faq->category_id,
                ];
            })->toArray();
    }

    private function getProductsData(Business $business, array $config): array
    {
        $query = $business->products()
            ->with('images', 'category')
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['product_ids'])) {
            $query->whereIn('id', $config['product_ids']);
        }

        return $query
            ->get(['id', 'name', 'slug', 'description', 'price', 'compare_at_price', 'sku', 'barcode', 'quantity', 'whatsapp_contact', 'image', 'category_id'])
            ->map(function ($product) {
                $imagePath = $product->image;
                if (!$imagePath && $product->images && $product->images->isNotEmpty()) {
                    $imagePath = $product->images->first()->path;
                }

                if ($imagePath) {
                    if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                        $finalPath = $imagePath;
                    } else {
                        $finalPath = "/storage/{$imagePath}";
                    }
                } else {
                    $finalPath = null;
                }

                $galleryImages = $product->images->map(function ($img) {
                    $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
                    return [
                        'id' => $img->id,
                        'path' => $path,
                        'title' => $img->title ?? '',
                    ];
                })->toArray();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'compare_at_price' => $product->compare_at_price,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'quantity' => $product->quantity,
                    'whatsapp_contact' => $product->whatsapp_contact,
                    'image' => $finalPath,
                    'gallery' => $galleryImages,
                    'category_id' => $product->category_id,
                ];
            })->toArray();
    }

    private function getReviewsData(Business $business, array $config): array
    {
        $query = $business->reviews()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['review_ids'])) {
            $query->whereIn('id', $config['review_ids']);
        }

        $maxItems = $config['max_items'] ?? 10;
        $query->limit($maxItems);

        return $query
            ->get(['id', 'client_name', 'company', 'comment', 'rating', 'google_link'])
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'client_name' => $review->client_name,
                    'company' => $review->company,
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'google_link' => $review->google_link,
                ];
            })->toArray();
    }

    private function getAiChatbotSettings($business): ?array
    {
        $aiSetting = \Modules\AiChatbot\Models\BusinessAiSetting::where('business_id', $business->id)
            ->where('is_enabled', true)
            ->first();

        if (!$aiSetting) {
            return null;
        }

        return [
            'is_enabled' => true,
            'widget_color' => $aiSetting->widget_color,
            'widget_theme' => $aiSetting->widget_theme ?? 'light',
            'allow_reset_chat' => $aiSetting->allow_reset_chat ?? false,
        ];
    }
}

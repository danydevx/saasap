<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;
use Modules\Services\Models\BusinessService;
use Modules\Gallery\Models\BusinessGalleryImage;
use Modules\Products\Models\BusinessProduct;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Appointments\Models\BusinessAppointmentSlot;
use Modules\Leads\Models\BusinessLead;
use Modules\Reviews\Models\BusinessReview;
use Modules\Promotions\Models\BusinessPromotion;
use Modules\Hero\Models\BusinessHero;
use Modules\SocialMedia\Models\BusinessSocialNetwork;

class BusinessController extends Controller
{
    protected $themeSections = [
        ['key' => 'hero', 'module' => null, 'label' => 'Hero Principal'],
        ['key' => 'about', 'module' => null, 'label' => 'Acerca de'],
        ['key' => 'services', 'module' => 'services', 'label' => 'Servicios'],
        ['key' => 'gallery', 'module' => 'gallery', 'label' => 'Galería'],
        ['key' => 'products', 'module' => 'products', 'label' => 'Productos'],
        ['key' => 'menu', 'module' => 'restaurant_menu', 'label' => 'Menú'],
        ['key' => 'appointments', 'module' => 'appointments', 'label' => 'Turnos'],
        ['key' => 'contact', 'module' => 'contact_form', 'label' => 'Contacto'],
        ['key' => 'reviews', 'module' => 'reviews', 'label' => 'Reseñas'],
        ['key' => 'locations', 'module' => 'locations', 'label' => 'Ubicaciones'],
        ['key' => 'promotions', 'module' => 'promotions', 'label' => 'Promociones'],
    ];

    public function show(string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->getEnabledModules();

        $theme = $business->minisiteTheme;

        $services = [];
        if (in_array('services', $modules)) {
            $services = $business->services()
                ->where('is_active', true)
                ->where('allows_online_booking', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'description', 'price', 'duration_minutes']);
        }

        $locations = [];
        if (in_array('locations', $modules)) {
            $locations = $business->locations()
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->get(['id', 'name', 'address_line_1', 'city']);
        }

        $gallery = [];
        if (in_array('gallery', $modules)) {
            $gallery = $business->galleryImages()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get();
        }

        $reviews = [];
        if (in_array('reviews', $modules)) {
            $reviews = $business->reviews()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }

        $promotions = [];
        if (in_array('promotions', $modules)) {
            $promotions = $business->promotions()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $products = [];
        if (in_array('products', $modules)) {
            $products = $business->products()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get(['id', 'name', 'description', 'price']);
        }

        $menuCategories = [];
        $menuProducts = [];
        if (in_array('restaurant_menu', $modules)) {
            $menuCategories = \Modules\RestaurantMenu\Entities\MenuCategory::where('business_id', $business->id)
                ->whereNull('parent_id')
                ->where('active', true)
                ->with(['children' => function ($q) {
                    $q->where('active', true)->orderBy('sort_order');
                }, 'products' => function ($q) {
                    $q->where('active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get()
                ->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'title' => $cat->title,
                        'image' => $cat->image,
                        'products' => $cat->products->map(function ($p) {
                            return [
                                'id' => $p->id,
                                'title' => $p->title,
                                'image' => $p->image,
                                'display_price' => $p->display_price,
                            ];
                        }),
                        'children' => $cat->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'title' => $child->title,
                                'image' => $child->image,
                                'products' => $child->products->map(function ($p) {
                                    return [
                                        'id' => $p->id,
                                        'title' => $p->title,
                                        'image' => $p->image,
                                        'display_price' => $p->display_price,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });
        }

        $socialNetworks = [];
        if (in_array('socialmedia', $modules)) {
            $socialNetworks = $business->socialNetworks()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact']);
        }

        $hero = $business->hero;

        return Inertia::render('Public/Business/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'description' => $business->description,
                'phone' => $business->phone,
                'email' => $business->email,
                'website' => $business->website,
                'timezone' => $business->timezone,
                'currency' => $business->currency,
                'business_type' => $business->business_type->value ?? $business->business_type,
                'logo_path' => $business->logo_path,
                'cover_image_path' => $business->cover_image_path,
            ],
            'theme' => $theme ? [
                'id' => $theme->id,
                'name' => $theme->name,
                'slug' => $theme->slug,
                'css_variables' => $theme->css_variables,
                'layout_config' => $theme->layout_config,
                'section_config' => $theme->section_config,
            ] : null,
            'themeSections' => $this->themeSections,
            'modules' => $modules,
            'services' => $services,
            'locations' => $locations,
            'gallery' => $gallery,
            'reviews' => $reviews,
            'promotions' => $promotions,
            'products' => $products,
            'menuCategories' => $menuCategories,
            'menuProducts' => $menuProducts,
            'socialNetworks' => $socialNetworks,
            'hero' => $hero ? [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'text_aux' => $hero->text_aux,
                'background_type' => $hero->background_type,
                'background_color' => $hero->background_color,
                'background_gradient_start' => $hero->background_gradient_start,
                'background_gradient_end' => $hero->background_gradient_end,
                'background_image_path' => $hero->background_image_path,
                'alignment' => $hero->alignment,
                'buttons' => $hero->buttons,
                'show_contact_info' => $hero->show_contact_info,
                'show_social_links' => $hero->show_social_links,
            ] : null,
        ]);
    }

    public function services(string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('services', $modules)) {
            abort(404);
        }

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->with('location')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'description', 'price', 'duration_minutes', 'whatsapp_contact', 'image']);

        return Inertia::render('Public/Business/Services', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
            ],
            'services' => $services,
        ]);
    }

    public function gallery(string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('gallery', $modules)) {
            abort(404);
        }

        $images = $business->galleryImages()
            ->where('is_active', true)
            ->with('location')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Public/Business/Gallery', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'images' => $images,
        ]);
    }

    public function products(string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('products', $modules)) {
            abort(404);
        }

        $products = $business->products()
            ->where('is_active', true)
            ->with('location')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'description', 'price', 'sku', 'quantity', 'whatsapp_contact']);

        return Inertia::render('Public/Business/Products', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
            ],
            'products' => $products,
        ]);
    }

    public function book(string $slug, Request $request)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('appointments', $modules)) {
            abort(404);
        }

        $serviceId = $request->input('service');
        $locationId = $request->input('location');

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city']);

        $selectedService = $serviceId
            ? $services->firstWhere('id', (int) $serviceId)
            : $services->first();

        $availableSlots = [];
        if ($selectedService) {
            $availableSlots = BusinessAppointmentSlot::where('business_id', $business->id)
                ->where('business_service_id', $selectedService->id)
                ->where('is_available', true)
                ->where('slots_available', '>', 0)
                ->where(function ($query) {
                    $query->where('specific_date', '>=', now()->toDateString())
                        ->orWhere('day_of_week', '>=', now()->dayOfWeek);
                })
                ->orderBy('specific_date')
                ->orderBy('start_time')
                ->limit(20)
                ->get();
        }

        return Inertia::render('Public/Business/Book', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'timezone' => $business->timezone,
            ],
            'services' => $services,
            'locations' => $locations,
            'selectedService' => $selectedService,
            'availableSlots' => $availableSlots,
            'selectedLocation' => $locationId ? $locations->firstWhere('id', (int) $locationId) : null,
        ]);
    }

    public function storeBooking(string $slug, Request $request)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('appointments', $modules)) {
            abort(404);
        }

        $data = $request->validate([
            'service_id' => ['required', 'exists:business_services,id'],
            'location_id' => ['required', 'exists:business_locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $service = BusinessService::findOrFail($data['service_id']);
        $location = BusinessLocation::findOrFail($data['location_id']);

        $endTime = date('H:i', strtotime($data['start_time'] . ' + ' . $service->duration_minutes . ' minutes'));

        $appointment = BusinessAppointment::create([
            'business_id' => $business->id,
            'business_location_id' => $location->id,
            'business_service_id' => $service->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('public.business.booking.success', $slug)
            ->with('appointment_id', $appointment->id);
    }

    public function bookingSuccess(string $slug, Request $request)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('Public/Business/BookingSuccess', [
            'business' => [
                'name' => $business->name,
            ],
        ]);
    }

    public function contact(string $slug, Request $request)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('contact_form', $modules)) {
            abort(404);
        }

        return Inertia::render('Public/Business/Contact', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'email' => $business->email,
                'phone' => $business->phone,
            ],
        ]);
    }

    public function storeContact(string $slug, Request $request)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (!in_array('contact_form', $modules)) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $lead = BusinessLead::create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['message'],
            'source' => 'website',
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Mensaje enviado correctamente.');
    }
}

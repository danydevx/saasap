<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Promotions\Models\BusinessPromotion;

class PromotionController extends Controller
{
    private const MAX_FILE_SIZE_KB = 5120;
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessPromotion::class, $business]);

        $promotions = $business->promotions()
            ->with('location')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Member/Promotions/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'promotions' => $promotions,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessPromotion::class, $business]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Promotions/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessPromotion::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'promotion_price' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['business_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        $promotion = $business->promotions()->create($data);

        $activity->log('promotion_created', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Promocion creada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.promotions.index', $business->id)
            ->with('success', 'Promocion creada correctamente.');
    }

    public function edit(Request $request, Business $business, BusinessPromotion $promotion)
    {
        $this->authorize('update', [BusinessPromotion::class, $promotion]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Promotions/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'promotion' => [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'slug' => $promotion->slug,
                'description' => $promotion->description,
                'image' => $promotion->image,
                'regular_price' => $promotion->regular_price,
                'promotion_price' => $promotion->promotion_price,
                'coupon_code' => $promotion->coupon_code,
                'starts_at' => $promotion->starts_at?->format('Y-m-d\TH:i'),
                'expires_at' => $promotion->expires_at?->format('Y-m-d\TH:i'),
                'business_location_id' => $promotion->business_location_id,
                'is_active' => $promotion->is_active,
                'sort_order' => $promotion->sort_order,
            ],
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Business $business, BusinessPromotion $promotion, ActivityService $activity)
    {
        $this->authorize('update', [BusinessPromotion::class, $promotion]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'promotion_price' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $promotion->update($data);

        $activity->log('promotion_updated', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Promocion actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.promotions.index', $business->id)
            ->with('success', 'Promocion actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessPromotion $promotion, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessPromotion::class, $promotion]);

        $activity->log('promotion_deleted', [
            'actor' => $request->user(),
            'subject' => $promotion,
            'description' => 'Promocion eliminada',
        ]);

        $promotion->delete();

        return redirect()->route('member.businesses.promotions.index', $business->id)
            ->with('success', 'Promocion eliminada correctamente.');
    }
}

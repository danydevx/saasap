<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;
use Modules\Products\Models\BusinessProduct;

class ProductController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessProduct::class, $business]);

        $products = $business->products()
            ->with('location')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Member/Products/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'products' => $products,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessProduct::class, $business]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Products/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessProduct::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $data['business_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);

        $product = $business->products()->create($data);

        $activity->log('product_created', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Producto creado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.products.index', $business->id)
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Request $request, Business $business, BusinessProduct $product)
    {
        $this->authorize('update', [BusinessProduct::class, $product]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Products/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
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
                'is_active' => $product->is_active,
                'is_featured' => $product->is_featured,
                'whatsapp_contact' => $product->whatsapp_contact,
                'sort_order' => $product->sort_order,
                'business_location_id' => $product->business_location_id,
            ],
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Business $business, BusinessProduct $product, ActivityService $activity)
    {
        $this->authorize('update', [BusinessProduct::class, $product]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        if (isset($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);
        }

        $product->update($data);

        $activity->log('product_updated', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Producto actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.products.index', $business->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessProduct $product, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessProduct::class, $product]);

        $activity->log('product_deleted', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Producto eliminado',
        ]);

        $product->delete();

        return redirect()->route('member.businesses.products.index', $business->id)
            ->with('success', 'Producto eliminado correctamente.');
    }
}

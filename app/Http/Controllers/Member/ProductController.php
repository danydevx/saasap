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

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'price', 'is_active', 'is_featured', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->products()
            ->with('location', 'category')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('name');

        $products = $query->paginate($perPage);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = $business->productCategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
        ];

        return Inertia::render('Member/Products/ProductsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'products' => $products,
            'locations' => $locations,
            'categories' => $categories,
            'dataTable' => $dataTable,
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
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'category_id' => ['nullable', 'exists:business_product_categories,id'],
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
        $categories = $business->productCategories()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

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
                'category_id' => $product->category_id,
            ],
            'locations' => $locations,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Business $business, BusinessProduct $product, ActivityService $activity)
    {
        $this->authorize('update', [BusinessProduct::class, $product]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'category_id' => ['nullable', 'exists:business_product_categories,id'],
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

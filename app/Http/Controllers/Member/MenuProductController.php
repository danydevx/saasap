<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\RestaurantMenu\Entities\MenuProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuProductController extends Controller
{
    public function index(Request $request, Business $business, ?MenuCategory $category = null)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $query = MenuProduct::where('business_id', $business->id)->with('category', 'variants', 'images');

        if ($category) {
            abort_unless($category->business_id === $business->id, 403);
            $query->where('category_id', $category->id);
        } elseif ($request->boolean('uncategorized')) {
            $query->whereNull('category_id');
        }

        $products = $query->orderBy('sort_order')->paginate(20);

        $categories = MenuCategory::where('business_id', $business->id)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'title' => $cat->nested_title,
                ];
            });

        return inertia('Member/Products/Index', [
            'business' => $business,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->boolean('uncategorized') ? 'uncategorized' : ($category?->id ?? null),
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png|max:10240',
            'base_price' => 'nullable|numeric|min:0',
            'show_price' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
            'variants' => 'nullable|array',
            'variants.*.title' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.description' => 'nullable|string',
            'variants.*.sort_order' => 'nullable|integer',
            'variants.*.active' => 'nullable|boolean',
        ]);

        $category = MenuCategory::find($validated['category_id']);
        abort_unless($category->business_id === $business->id, 403);

        $validated['business_id'] = $business->id;
        $validated['slug'] = MenuProduct::generateUniqueSlug($business->id, $validated['title']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products/' . $business->id, ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        }

        $variants = $validated['variants'] ?? [];
        unset($validated['variants']);

        $product = MenuProduct::create($validated);

        if (!empty($variants)) {
            foreach ($variants as $variantData) {
                $product->variants()->create($variantData);
            }
        }

        return redirect()->back()->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, Business $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->business_id === $business->id, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png|max:10240',
            'base_price' => 'nullable|numeric|min:0',
            'show_price' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
            'variants' => 'nullable|array',
            'variants.*.title' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.description' => 'nullable|string',
            'variants.*.sort_order' => 'nullable|integer',
            'variants.*.active' => 'nullable|boolean',
        ]);

        $category = MenuCategory::find($validated['category_id']);
        abort_unless($category->business_id === $business->id, 403);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products/' . $business->id, ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['image']);
        }

        $variants = $validated['variants'] ?? null;
        unset($validated['variants']);

        $product->update($validated);

        if (is_array($variants)) {
            $product->variants()->delete();
            foreach ($variants as $variantData) {
                $product->variants()->create($variantData);
            }
        }

        return redirect()->back()->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Business $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->business_id === $business->id, 403);

        $product->variants()->delete();
        $product->images()->delete();
        $product->delete();

        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }
}
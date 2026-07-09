<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Products\Models\BusinessProductCategory;
use Illuminate\Support\Facades\Auth;

class ProductCategoryController extends Controller
{
    public function index(Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $categories = BusinessProductCategory::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->with('children', 'products')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Member/Products/CategoriesIndex', [
            'business' => $business,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:business_product_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['business_id'] = $business->id;
        $validated['slug'] = BusinessProductCategory::generateUniqueSlug($business->id, $validated['name']);

        BusinessProductCategory::create($validated);

        return redirect()->back()->with('success', 'Categoria creada exitosamente.');
    }

    public function update(Request $request, Business $business, BusinessProductCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:business_product_categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('Una categoria no puede ser padre de si misma.');
                    }
                },
            ],
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Categoria actualizada exitosamente.');
    }

    public function destroy(Business $business, BusinessProductCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        if ($category->children()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar una categoria con subcategorias.');
        }

        $category->products()->update(['category_id' => null]);

        $category->delete();

        return redirect()->back()->with('success', 'Categoria eliminada. Los productos fueron desvinculados.');
    }
}

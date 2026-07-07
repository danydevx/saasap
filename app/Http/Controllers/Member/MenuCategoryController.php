<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Illuminate\Support\Facades\Auth;

class MenuCategoryController extends Controller
{
    public function index(Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $categories = MenuCategory::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->with('children', 'products')
            ->orderBy('sort_order')
            ->get();

        return inertia('Member/Categories/Index', [
            'business' => $business,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:menu_categories,id',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['business_id'] = $business->id;
        $validated['slug'] = MenuCategory::generateUniqueSlug($business->id, $validated['title']);

        $category = MenuCategory::create($validated);

        return redirect()->back()->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, Business $business, MenuCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:menu_categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('Una categoría no puede ser padre de sí misma.');
                    }
                },
            ],
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Business $business, MenuCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        if ($category->children()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar una categoría con subcategorías.');
        }

        $category->products()->update(['category_id' => null]);

        $category->delete();

        return redirect()->back()->with('success', 'Categoría eliminada. Los productos fueron desvinculados.');
    }
}
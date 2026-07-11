<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Faqs\Models\BusinessFaqCategory;
use Illuminate\Support\Facades\Auth;

class FaqCategoryController extends Controller
{
    public function index(Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $categories = BusinessFaqCategory::where('business_id', $business->id)
            ->with('faqs')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Member/Faqs/Categories/Index', [
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
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['business_id'] = $business->id;
        $validated['slug'] = BusinessFaqCategory::generateUniqueSlug($business->id, $validated['name']);

        BusinessFaqCategory::create($validated);

        return redirect()->back()->with('success', 'Categoria creada exitosamente.');
    }

    public function update(Request $request, Business $business, BusinessFaqCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Categoria actualizada exitosamente.');
    }

    public function destroy(Business $business, BusinessFaqCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        $category->faqs()->update(['category_id' => null]);

        $category->delete();

        return redirect()->back()->with('success', 'Categoria eliminada. Las preguntas fueron desvinculadas.');
    }
}

<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Modules\Businesses\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    private const MAX_LOGO_SIZE_KB = 2048;
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function edit(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        return inertia('Member/Businesses/Edit', [
            'business' => $business,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp', 'max:' . self::MAX_LOGO_SIZE_KB],
            'remove_logo' => ['boolean'],
        ], [
            'logo.mimes' => 'El logo debe ser una imagen JPG, PNG, GIF o WebP.',
            'logo.max' => 'El logo no puede superar los 2MB.',
        ]);

        if ($request->boolean('remove_logo')) {
            if ($business->logo_path) {
                $path = str_replace(url('/') . '/storage/', '', $business->logo_path);
                Storage::disk('public')->delete($path);
            }
            $validated['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($business->logo_path) {
                $oldPath = str_replace(url('/') . '/storage/', '', $business->logo_path);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('logo')->store('businesses/' . $business->id, ['disk' => 'public']);
            $validated['logo_path'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['logo_path']);
        }

        $business->update($validated);

        return redirect()->back()->with('success', 'Negocio actualizado correctamente.');
    }
}

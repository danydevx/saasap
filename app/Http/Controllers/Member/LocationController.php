<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;

class LocationController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLocation::class, $business]);

        $locations = $business->locations()
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Member/Locations/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessLocation::class, $business]);

        return Inertia::render('Member/Locations/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessLocation::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'directions_url' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $location = $business->locations()->create($data);

        $activity->log('location_created', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Ubicacion creada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.locations.index', $business->id)
            ->with('success', 'Ubicacion creada correctamente.');
    }

    public function edit(Request $request, Business $business, BusinessLocation $location)
    {
        $this->authorize('update', [BusinessLocation::class, $location]);

        return Inertia::render('Member/Locations/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'address_line_1' => $location->address_line_1,
                'address_line_2' => $location->address_line_2,
                'city' => $location->city,
                'state' => $location->state,
                'postal_code' => $location->postal_code,
                'country' => $location->country,
                'phone' => $location->phone,
                'email' => $location->email,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'directions_url' => $location->directions_url,
                'is_primary' => $location->is_primary,
                'is_active' => $location->is_active,
            ],
        ]);
    }

    public function update(Request $request, Business $business, BusinessLocation $location, ActivityService $activity)
    {
        $this->authorize('update', [BusinessLocation::class, $location]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'directions_url' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $location->update($data);

        $activity->log('location_updated', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Ubicacion actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.locations.index', $business->id)
            ->with('success', 'Ubicacion actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessLocation $location, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessLocation::class, $location]);

        $activity->log('location_deleted', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Ubicacion eliminada',
        ]);

        $location->delete();

        return redirect()->route('member.businesses.locations.index', $business->id)
            ->with('success', 'Ubicacion eliminada correctamente.');
    }
}

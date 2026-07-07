<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;
use Modules\Services\Models\BusinessService;

class ServiceController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessService::class, $business]);

        $services = $business->services()
            ->with('location')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Member/Services/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'services' => $services,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessService::class, $business]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Services/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessService::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['business_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);

        $service = $business->services()->create($data);

        $activity->log('service_created', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio creado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.services.index', $business->id)
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Request $request, Business $business, BusinessService $service)
    {
        $this->authorize('update', [BusinessService::class, $service]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Services/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'image' => $service->image,
                'duration_minutes' => $service->duration_minutes,
                'price' => $service->price,
                'deposit_required' => $service->deposit_required,
                'deposit_amount' => $service->deposit_amount,
                'allows_online_booking' => $service->allows_online_booking,
                'whatsapp_contact' => $service->whatsapp_contact,
                'is_active' => $service->is_active,
                'sort_order' => $service->sort_order,
                'business_location_id' => $service->business_location_id,
            ],
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Business $business, BusinessService $service, ActivityService $activity)
    {
        $this->authorize('update', [BusinessService::class, $service]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        if (isset($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);
        }

        $service->update($data);

        $activity->log('service_updated', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.services.index', $business->id)
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessService $service, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessService::class, $service]);

        $activity->log('service_deleted', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio eliminado',
        ]);

        $service->delete();

        return redirect()->route('member.businesses.services.index', $business->id)
            ->with('success', 'Servicio eliminado correctamente.');
    }
}

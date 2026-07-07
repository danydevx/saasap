<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;
use Modules\Leads\Enums\LeadStatus;
use Modules\Leads\Enums\LeadSource;
use Modules\Locations\Models\BusinessLocation;

class LeadController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $leads = $business->leads()
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Member/Leads/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'leads' => $leads,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessLead::class, $business]);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Leads/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessLead::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'source' => ['nullable', 'string'],
        ]);

        $lead = $business->leads()->create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'business_location_id' => $data['business_location_id'] ?? null,
            'status' => LeadStatus::NEW,
            'source' => $data['source'] ? LeadSource::from($data['source']) : LeadSource::MANUAL,
        ]);

        $activity->log('lead_created', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Lead creado manualmente',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.leads.index', $business->id)
            ->with('success', 'Contacto creado correctamente.');
    }

    public function show(Request $request, Business $business, BusinessLead $lead)
    {
        $this->authorize('view', [BusinessLead::class, $lead]);

        $lead->load('location');

        return Inertia::render('Member/Leads/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'lead' => [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'notes' => $lead->notes,
                'status' => $lead->status->value,
                'status_label' => $lead->status->label(),
                'source' => $lead->source->value,
                'source_label' => $lead->source->label(),
                'created_at' => $lead->created_at->toDateTimeString(),
                'business_location_id' => $lead->business_location_id,
                'location' => $lead->location ? [
                    'id' => $lead->location->id,
                    'name' => $lead->location->name,
                ] : null,
            ],
        ]);
    }

    public function edit(Request $request, Business $business, BusinessLead $lead)
    {
        $this->authorize('update', [BusinessLead::class, $lead]);

        $lead->load('location');

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Leads/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'lead' => [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'notes' => $lead->notes,
                'status' => $lead->status->value,
                'source' => $lead->source->value,
                'business_location_id' => $lead->business_location_id,
            ],
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Business $business, BusinessLead $lead, ActivityService $activity)
    {
        $this->authorize('update', [BusinessLead::class, $lead]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'status' => ['required', 'string', 'in:new,contacted,qualified,converted,lost'],
            'source' => ['nullable', 'string'],
        ]);

        $lead->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'business_location_id' => $data['business_location_id'] ?? null,
            'status' => LeadStatus::from($data['status']),
            'source' => $data['source'] ? LeadSource::from($data['source']) : LeadSource::MANUAL,
        ]);

        $activity->log('lead_updated', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Lead actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.leads.index', $business->id)
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessLead $lead, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessLead::class, $lead]);

        $activity->log('lead_deleted', [
            'actor' => $request->user(),
            'subject' => $lead,
            'description' => 'Lead eliminado',
        ]);

        $lead->delete();

        return redirect()->route('member.businesses.leads.index', $business->id)
            ->with('success', 'Contacto eliminado correctamente.');
    }
}

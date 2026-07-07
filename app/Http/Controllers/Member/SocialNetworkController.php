<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\SocialMedia\Models\BusinessSocialNetwork;

class SocialNetworkController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessSocialNetwork::class, $business]);

        $socialNetworks = $business->socialNetworks()->orderBy('sort_order')->get();

        return Inertia::render('Member/SocialNetworks/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'socialNetworks' => $socialNetworks,
            'platforms' => BusinessSocialNetwork::$platforms,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessSocialNetwork::class, $business]);

        $data = $request->validate([
            'platform' => ['required', 'string'],
            'url' => ['required', 'string', 'max:500', 'url'],
            'username' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'show_on_hero' => ['boolean'],
            'show_on_footer' => ['boolean'],
            'show_on_contact' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $data['business_id'] = $business->id;

        $socialNetwork = BusinessSocialNetwork::create($data);

        $activity->log('social_network_created', [
            'actor' => $request->user(),
            'subject' => $socialNetwork,
            'description' => 'Red social creada por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Red social creada correctamente.');
    }

    public function update(Request $request, Business $business, BusinessSocialNetwork $socialNetwork, ActivityService $activity)
    {
        $this->authorize('update', $socialNetwork);

        $data = $request->validate([
            'url' => ['required', 'string', 'max:500', 'url'],
            'username' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'show_on_hero' => ['boolean'],
            'show_on_footer' => ['boolean'],
            'show_on_contact' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $socialNetwork->update($data);

        $activity->log('social_network_updated', [
            'actor' => $request->user(),
            'subject' => $socialNetwork,
            'description' => 'Red social actualizada por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Red social actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessSocialNetwork $socialNetwork, ActivityService $activity)
    {
        $this->authorize('delete', $socialNetwork);

        $socialNetwork->delete();

        $activity->log('social_network_deleted', [
            'actor' => $request->user(),
            'subject' => $socialNetwork,
            'description' => 'Red social eliminada por miembro',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Red social eliminada correctamente.');
    }
}

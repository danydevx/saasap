<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;

class ContactFormController extends Controller
{
    public function submissions(Request $request, Business $business)
    {
        $this->authorize('viewAny', [\Modules\Leads\Models\BusinessLead::class, $business]);

        $submissions = $business->leads()
            ->where('source', 'website')
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Member/ContactForm/Submissions', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'submissions' => $submissions,
        ]);
    }
}

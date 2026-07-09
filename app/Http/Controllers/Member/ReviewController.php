<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Reviews\Models\BusinessReview;

class ReviewController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessReview::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['client_name', 'rating', 'is_active', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->reviews()
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $reviews = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($reviews->items())->map(function ($review) {
                return [
                    'id' => $review->id,
                    'client_name' => $review->client_name,
                    'company' => $review->company,
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'google_link' => $review->google_link,
                    'is_active' => $review->is_active,
                    'location' => $review->location ? [
                        'id' => $review->location->id,
                        'name' => $review->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'per_page' => $reviews->perPage(),
            'total' => $reviews->total(),
            'from' => $reviews->firstItem(),
            'to' => $reviews->lastItem(),
        ];

        return Inertia::render('Member/Reviews/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'reviews' => $reviews,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessReview::class, $business]);

        return Inertia::render('Member/Reviews/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessReview::class, $business]);

        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'comment' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'google_link' => ['nullable', 'url', 'max:500'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['business_id'] = $business->id;

        $review = $business->reviews()->create($data);

        $activity->log('review_created', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Review created',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.reviews.index', $business->id)
            ->with('success', 'Review created successfully.');
    }

    public function edit(Request $request, Business $business, BusinessReview $review)
    {
        $this->authorize('update', [BusinessReview::class, $review]);

        return Inertia::render('Member/Reviews/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'review' => [
                'id' => $review->id,
                'client_name' => $review->client_name,
                'company' => $review->company,
                'comment' => $review->comment,
                'rating' => $review->rating,
                'google_link' => $review->google_link,
                'business_location_id' => $review->business_location_id,
                'is_active' => $review->is_active,
                'sort_order' => $review->sort_order,
            ],
        ]);
    }

    public function update(Request $request, Business $business, BusinessReview $review, ActivityService $activity)
    {
        $this->authorize('update', [BusinessReview::class, $review]);

        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'comment' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'google_link' => ['nullable', 'url', 'max:500'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $review->update($data);

        $activity->log('review_updated', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Review updated',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.reviews.index', $business->id)
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Request $request, Business $business, BusinessReview $review, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessReview::class, $review]);

        $activity->log('review_deleted', [
            'actor' => $request->user(),
            'subject' => $review,
            'description' => 'Review deleted',
        ]);

        $review->delete();

        return redirect()->route('member.businesses.reviews.index', $business->id)
            ->with('success', 'Review deleted successfully.');
    }
}

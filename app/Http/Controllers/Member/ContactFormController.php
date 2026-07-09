<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;

class ContactFormController extends Controller
{
    public function submissions(Request $request, Business $business)
    {
        $this->authorize('viewAny', [\Modules\Leads\Models\BusinessLead::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'email', 'status', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->leads()
            ->where('source', 'website')
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $submissions = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($submissions->items())->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'notes' => $lead->notes,
                    'status' => $lead->status->value,
                    'status_label' => $lead->status->label(),
                    'created_at' => $lead->created_at->toDateTimeString(),
                    'location' => $lead->location ? [
                        'id' => $lead->location->id,
                        'name' => $lead->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $submissions->currentPage(),
            'last_page' => $submissions->lastPage(),
            'per_page' => $submissions->perPage(),
            'total' => $submissions->total(),
            'from' => $submissions->firstItem(),
            'to' => $submissions->lastItem(),
        ];

        return Inertia::render('Member/ContactForm/Submissions', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'submissions' => $submissions,
            'dataTable' => $dataTable,
        ]);
    }

    public function export(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $leads = $business->leads()
            ->where('source', 'website')
            ->with('location')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contactos_' . $business->id . '_' . date('Y-m-d') . '.csv"',
            'Cache-Control' => 'no-store, no-cache',
        ];

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Nombre', 'Email', 'Telefono', 'Notas', 'Estado', 'Ubicacion', 'Fecha']);

        foreach ($leads as $lead) {
            fputcsv($handle, [
                $lead->name,
                $lead->email,
                $lead->phone ?? '',
                $lead->notes ?? '',
                $lead->status->label() ?? '',
                $lead->location?->name ?? '',
                $lead->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }
}

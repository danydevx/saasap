<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Gallery\Models\BusinessGalleryImage;

class GalleryController extends Controller
{
    private const MAX_FILE_SIZE_KB = 5120;
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessGalleryImage::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['title', 'sort_order', 'is_active', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->galleryImages()
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderByDesc('id');

        $images = $query->paginate($perPage);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => collect($images->items())->map(function ($img) {
                return [
                    'id' => $img->id,
                    'path' => $img->path,
                    'title' => $img->title,
                    'description' => $img->description,
                    'sort_order' => $img->sort_order,
                    'is_active' => $img->is_active,
                    'location' => $img->location ? [
                        'id' => $img->location->id,
                        'name' => $img->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $images->currentPage(),
            'last_page' => $images->lastPage(),
            'per_page' => $images->perPage(),
            'total' => $images->total(),
            'from' => $images->firstItem(),
            'to' => $images->lastItem(),
        ];

        return Inertia::render('Member/Gallery/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'images' => $images,
            'locations' => $locations,
            'maxSizeKb' => self::MAX_FILE_SIZE_KB,
            'allowedTypes' => self::ALLOWED_MIME_TYPES,
            'dataTable' => $dataTable,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [BusinessGalleryImage::class, $business]);

        $request->validate([
            'file' => ['required', 'file', 'max:' . self::MAX_FILE_SIZE_KB, 'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES)],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ], [
            'file.max' => 'El archivo supera el tamaño máximo de 5MB.',
            'file.mimetypes' => 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).',
        ]);

        $file = $request->file('file');
        $disk = 'public';
        $path = $file->store('gallery/' . $business->id, ['disk' => $disk]);

        $image = $business->galleryImages()->create([
            'business_id' => $business->id,
            'path' => Storage::disk($disk)->url($path),
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'business_location_id' => $request->input('business_location_id'),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $activity->log('gallery_image_uploaded', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen subida a galeria',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Imagen subida correctamente.');
    }

    public function update(Request $request, Business $business, BusinessGalleryImage $image, ActivityService $activity)
    {
        $this->authorize('update', [BusinessGalleryImage::class, $image]);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $image->update($data);

        $activity->log('gallery_image_updated', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen de galeria actualizada',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessGalleryImage $image, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessGalleryImage::class, $image]);

        if ($image->path) {
            $path = str_replace(url('/') . '/storage/', '', $image->path);
            Storage::disk('public')->delete($path);
        }

        $activity->log('gallery_image_deleted', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen de galeria eliminada',
        ]);

        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}

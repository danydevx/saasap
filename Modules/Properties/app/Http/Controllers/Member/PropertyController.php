<?php

namespace Modules\Properties\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Services\ActivityService;
use App\Services\Properties\PropertyFormSchemaService;
use App\Services\Properties\PropertyImageService;
use App\Services\Properties\PropertyLimitService;
use App\Services\Properties\PropertyService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyType;

class PropertyController extends Controller
{
    public function __construct(
        protected PropertyService $propertyService,
        protected PropertyFormSchemaService $formSchemaService,
        protected PropertyImageService $imageService
    ) {}

    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [Property::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $propertyTypeId = $request->get('property_type');
        $operationType = $request->get('operation');
        $status = $request->get('status');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $filters = array_filter([
            'search' => $search,
            'property_type_id' => $propertyTypeId,
            'operation_type' => $operationType,
            'status' => $status,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
            'direction' => $direction,
        ]);

        $query = $this->propertyService->getPropertiesQuery($business, $filters);

        $properties = $query->paginate($perPage);

        $properties->getCollection()->transform(function ($property) {
            $item = $property->toArray();
            $item['main_image_url'] = $this->imageService->getImageUrl($property->main_image);
            $item['formatted_price'] = $property->getFormattedPrice();
            $item['operation_label'] = $property->getOperationLabel();
            $item['status_label'] = $property->getStatusLabel();
            return $item;
        });

        $propertyTypes = PropertyType::active()->orderBy('name')->get(['id', 'name', 'key']);

        $dataTable = [
            'data' => $properties->items(),
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
            'per_page' => $properties->perPage(),
            'total' => $properties->total(),
            'from' => $properties->firstItem(),
            'to' => $properties->lastItem(),
        ];

        $statusOptions = Property::STATUSES;
        $operationOptions = Property::OPERATIONS;

        return Inertia::render('Member/Properties/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'properties' => $properties,
            'propertyTypes' => $propertyTypes,
            'dataTable' => $dataTable,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'operationOptions' => $operationOptions,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [Property::class, $business]);

        $limitCheck = (new PropertyLimitService())->forBusiness($business)->canCreateProperty();

        if (! $limitCheck['allowed']) {
            return redirect()->back()->with('error', $limitCheck['reason']);
        }

        $propertyTypes = PropertyType::active()->orderBy('name')->get(['id', 'name', 'key', 'icon']);

        $selectedTypeId = $request->get('type');
        $formSchema = null;

        if ($selectedTypeId) {
            $propertyType = PropertyType::find($selectedTypeId);
            if ($propertyType) {
                $formSchema = $this->formSchemaService->getFormSchema($propertyType);
            }
        }

        return Inertia::render('Member/Properties/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'propertyTypes' => $propertyTypes,
            'selectedTypeId' => $selectedTypeId,
            'formSchema' => $formSchema,
            'limitInfo' => $limitCheck,
        ]);
    }

    public function store(StorePropertyRequest $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [Property::class, $business]);

        $limitCheck = (new PropertyLimitService())->forBusiness($business)->canCreateProperty();

        if (! $limitCheck['allowed']) {
            return redirect()->back()->with('error', $limitCheck['reason']);
        }

        $data = $request->validated();

        $property = $this->propertyService->createProperty($business, $data);

        $activity->log('property_created', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Propiedad creada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.properties.index', $business->id)
            ->with('success', 'Propiedad creada correctamente.');
    }

    public function edit(Request $request, Business $business, Property $property)
    {
        $this->authorize('update', [property::class, $property]);

        $propertyType = $property->propertyType;
        $formSchema = $this->formSchemaService->getFormSchema($propertyType);

        $property->load(['values.propertyField']);

        $dynamicValues = [];
        foreach ($property->values as $value) {
            $fieldKey = $value->propertyField->field_key ?? null;
            if ($fieldKey) {
                $dynamicValues[$fieldKey] = $value->getValue();
            }
        }

        return Inertia::render('Member/Properties/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'property' => [
                'id' => $property->id,
                'title' => $property->title,
                'slug' => $property->slug,
                'description' => $property->description,
                'operation_type' => $property->operation_type,
                'price' => $property->price,
                'currency' => $property->currency,
                'price_period' => $property->price_period,
                'main_image' => $property->main_image,
                'main_image_url' => $this->imageService->getImageUrl($property->main_image),
                'status' => $property->status,
                'is_featured' => $property->is_featured,
                'is_public' => $property->is_public,
                'property_type_id' => $property->property_type_id,
            ],
            'propertyType' => [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
                'key' => $propertyType->key,
            ],
            'formSchema' => $formSchema,
            'dynamicValues' => $dynamicValues,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Business $business, Property $property, ActivityService $activity)
    {
        $this->authorize('update', [Property::class, $property]);

        $data = $request->validated();

        $property = $this->propertyService->updateProperty($property, $data);

        $activity->log('property_updated', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Propiedad actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.properties.index', $business->id)
            ->with('success', 'Propiedad actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, Property $property, ActivityService $activity)
    {
        $this->authorize('delete', [Property::class, $property]);

        $this->propertyService->deleteProperty($property);

        $activity->log('property_deleted', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Propiedad eliminada',
        ]);

        return redirect()->route('member.businesses.properties.index', $business->id)
            ->with('success', 'Propiedad eliminada correctamente.');
    }

    public function duplicate(Request $request, Business $business, Property $property, ActivityService $activity)
    {
        $this->authorize('update', [Property::class, $property]);

        $limitCheck = (new PropertyLimitService())->forBusiness($business)->canCreateProperty();

        if (! $limitCheck['allowed']) {
            return redirect()->back()->with('error', $limitCheck['reason']);
        }

        $newProperty = $this->propertyService->duplicateProperty($property);

        $activity->log('property_duplicated', [
            'actor' => $request->user(),
            'subject' => $newProperty,
            'description' => 'Propiedad duplicada',
        ]);

        return redirect()->route('member.businesses.properties.edit', [$business->id, $newProperty->id])
            ->with('success', 'Propiedad duplicada correctamente.');
    }

    public function changeStatus(Request $request, Business $business, Property $property, ActivityService $activity)
    {
        $this->authorize('update', [Property::class, $property]);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Property::STATUSES)],
        ]);

        $property = $this->propertyService->changeStatus($property, $data['status']);

        $activity->log('property_status_changed', [
            'actor' => $request->user(),
            'subject' => $property,
            'description' => 'Estado cambiado a ' . $property->getStatusLabel(),
        ]);

        return redirect()->back()
            ->with('success', "Estado cambiado a {$property->getStatusLabel()}.");
    }

    public function reorder(Request $request, Business $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $ids = $data['ids'];
        $start = 1;

        foreach ($ids as $id) {
            Property::where('id', $id)
                ->where('business_id', $business->id)
                ->update(['sort_order' => $start++]);
        }

        return back(303);
    }

    public function bulkDelete(Request $request, Business $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $properties = Property::where('business_id', $business->id)
            ->whereIn('id', $data['ids'])
            ->get();

        foreach ($properties as $property) {
            $this->propertyService->deleteProperty($property);
        }

        $count = count($data['ids']);
        $message = $count === 1
            ? "1 propiedad eliminada correctamente."
            : "{$count} propiedades eliminadas correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }

    public function getFormSchema(Request $request, Business $business)
    {
        $this->authorize('viewAny', [Property::class, $business]);

        $typeId = $request->get('type_id');

        if (! $typeId) {
            return response()->json(['error' => 'type_id es requerido'], 400);
        }

        $propertyType = PropertyType::find($typeId);

        if (! $propertyType) {
            return response()->json(['error' => 'Tipo de propiedad no encontrado'], 404);
        }

        $schema = $this->formSchemaService->getFormSchema($propertyType);

        return response()->json($schema);
    }
}

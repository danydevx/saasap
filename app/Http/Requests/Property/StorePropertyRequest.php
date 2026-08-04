<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Properties\Models\Property;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $business = $this->route('business');
        $propertyTypeId = $this->input('property_type_id');

        return [
            'property_type_id' => [
                'required',
                'exists:property_types,id',
                Rule::exists('property_types', 'id')->where('is_active', true),
            ],
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                Rule::unique('properties')->where('business_id', $business?->id),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'operation_type' => [
                'required',
                Rule::in(Property::OPERATIONS),
            ],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'currency' => [
                'required',
                Rule::in(Property::CURRENCIES),
            ],
            'price_period' => [
                'required_if:operation_type,' . Property::OPERATION_RENT,
                Rule::in(Property::PERIODS),
            ],
            'main_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'status' => [
                'nullable',
                Rule::in(Property::STATUSES),
            ],
            'is_featured' => ['nullable', 'boolean'],
            'is_public' => ['nullable', 'boolean'],
            'dynamic_values' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_type_id.required' => 'Debes seleccionar un tipo de propiedad.',
            'property_type_id.exists' => 'El tipo de propiedad seleccionado no es válido.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 180 caracteres.',
            'operation_type.required' => 'El tipo de operación es obligatorio.',
            'operation_type.in' => 'El tipo de operación no es válido.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'currency.required' => 'La moneda es obligatoria.',
            'currency.in' => 'La moneda seleccionada no es válida.',
            'price_period.required_if' => 'La periodicidad es obligatoria para rentas.',
            'main_image.image' => 'La imagen debe ser un archivo de imagen.',
            'main_image.mimes' => 'La imagen debe ser JPG, PNG o WebP.',
            'main_image.max' => 'La imagen no puede exceder 5MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $price = $this->input('price');
            if (is_string($price)) {
                $price = str_replace([',', '$', ' '], '', $price);
                $this->merge(['price' => $price]);
            }
        }
    }
}

<?php

namespace App\Services\Properties;

use Modules\Properties\Models\PropertyType;
use Modules\Properties\Models\PropertyField;

class PropertyFormSchemaService
{
    public function getFormSchema(PropertyType $propertyType, ?int $excludeFieldId = null): array
    {
        $sections = [];

        foreach ($propertyType->activeSections as $section) {
            $fields = $section->activeFields;

            if ($excludeFieldId) {
                $fields = $fields->where('id', '!=', $excludeFieldId);
            }

            $sectionData = [
                'id' => $section->id,
                'name' => $section->name,
                'description' => $section->description,
                'sort_order' => $section->sort_order,
                'fields' => [],
            ];

            foreach ($fields as $field) {
                $sectionData['fields'][] = $this->formatField($field);
            }

            if (! empty($sectionData['fields'])) {
                $sections[] = $sectionData;
            }
        }

        $standaloneFields = $propertyType->activeFields()
            ->whereNull('section_id')
            ->when($excludeFieldId, fn($q) => $q->where('id', '!=', $excludeFieldId))
            ->get();

        if ($standaloneFields->isNotEmpty()) {
            $sections[] = [
                'id' => null,
                'name' => 'Información general',
                'description' => null,
                'sort_order' => 0,
                'fields' => $standaloneFields->map(fn($f) => $this->formatField($f))->toArray(),
            ];
        }

        usort($sections, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        return [
            'property_type' => [
                'id' => $propertyType->id,
                'name' => $propertyType->name,
                'key' => $propertyType->key,
            ],
            'sections' => $sections,
        ];
    }

    public function formatField(PropertyField $field): array
    {
        $data = [
            'id' => $field->id,
            'field_key' => $field->field_key,
            'field_type' => $field->field_type,
            'label' => $field->label,
            'description' => $field->description,
            'help_text' => $field->help_text,
            'placeholder' => $field->placeholder,
            'default_value' => $field->default_value,
            'is_required' => $field->is_required,
            'sort_order' => $field->sort_order,
            'options' => null,
        ];

        if (in_array($field->field_type, ['select', 'multiselect', 'radio', 'checkbox'])) {
            $data['options'] = $field->activeOptions->map(fn($opt) => [
                'value' => $opt->value,
                'label' => $opt->label,
            ])->toArray();
        }

        return $data;
    }

    public function getPublicSchema(PropertyType $propertyType): array
    {
        $sections = [];

        foreach ($propertyType->activeSections as $section) {
            $fields = $section->activeFields()->where('is_public', true)->get();

            if ($fields->isEmpty()) {
                continue;
            }

            $sections[] = [
                'name' => $section->name,
                'fields' => $fields->map(fn($f) => $this->formatField($f))->toArray(),
            ];
        }

        return [
            'property_type' => [
                'key' => $propertyType->key,
                'name' => $propertyType->name,
                'icon' => $propertyType->icon,
            ],
            'sections' => $sections,
        ];
    }

    public function getFilterableFields(PropertyType $propertyType): array
    {
        return $propertyType->activeFields()
            ->where('is_filterable', true)
            ->get()
            ->map(fn($f) => $this->formatField($f))
            ->toArray();
    }
}

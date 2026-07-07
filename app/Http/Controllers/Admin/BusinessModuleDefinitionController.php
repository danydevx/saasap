<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessModuleDefinition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusinessModuleDefinitionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $definitions = BusinessModuleDefinition::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/BusinessModuleDefinitions/Index', [
            'definitions' => $definitions,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/BusinessModuleDefinitions/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:100', 'unique:business_module_definitions,key'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'has_settings' => ['boolean'],
        ]);

        BusinessModuleDefinition::create([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'has_settings' => (bool) ($data['has_settings'] ?? false),
            'is_active' => true,
        ]);

        return redirect()->route('admin.business-module-definitions.index')
            ->with('success', 'Modulo creado correctamente.');
    }

    public function edit(BusinessModuleDefinition $definition)
    {
        return Inertia::render('Admin/BusinessModuleDefinitions/Edit', [
            'definition' => [
                'id' => $definition->id,
                'key' => $definition->key,
                'name' => $definition->name,
                'description' => $definition->description,
                'icon' => $definition->icon,
                'sort_order' => $definition->sort_order,
                'has_settings' => (bool) $definition->has_settings,
                'is_active' => (bool) $definition->is_active,
            ],
        ]);
    }

    public function update(Request $request, BusinessModuleDefinition $definition)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:100', 'unique:business_module_definitions,key,' . $definition->id],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'has_settings' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $definition->update([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'has_settings' => (bool) ($data['has_settings'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.business-module-definitions.index')
            ->with('success', 'Modulo actualizado correctamente.');
    }

    public function destroy(BusinessModuleDefinition $definition)
    {
        $definition->delete();

        return redirect()->route('admin.business-module-definitions.index')
            ->with('success', 'Modulo eliminado correctamente.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessModuleDefinition extends Model
{
    use HasFactory;

    protected $table = 'business_module_definitions';

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'sort_order',
        'is_active',
        'has_settings',
        'settings_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_settings' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function planModules(): HasMany
    {
        return $this->hasMany(PlanBusinessModule::class, 'module_definition_id');
    }

    public function businessModules(): HasMany
    {
        return $this->hasMany(BusinessModule::class, 'module_definition_id');
    }
}

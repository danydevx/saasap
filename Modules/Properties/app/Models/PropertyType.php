<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyType extends Model
{
    use HasFactory;

    protected $table = 'property_types';

    protected $fillable = [
        'name',
        'key',
        'slug',
        'description',
        'icon',
        'is_active',
        'is_public',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
        'settings' => 'array',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(PropertyField::class)->orderBy('sort_order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(PropertyField::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PropertyFieldSection::class)->orderBy('sort_order');
    }

    public function activeSections(): HasMany
    {
        return $this->hasMany(PropertyFieldSection::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}

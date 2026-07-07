<?php

namespace Modules\Promotions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessPromotion extends Model
{
    protected $fillable = [
        'business_id',
        'business_location_id',
        'name',
        'slug',
        'description',
        'image',
        'regular_price',
        'promotion_price',
        'coupon_code',
        'starts_at',
        'expires_at',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'promotion_price' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function getDiscountPercentage(): ?float
    {
        if (!$this->regular_price || !$this->promotion_price) {
            return null;
        }

        return round((($this->regular_price - $this->promotion_price) / $this->regular_price) * 100, 1);
    }
}

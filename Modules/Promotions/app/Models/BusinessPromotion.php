<?php

namespace Modules\Promotions\Models;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BusinessPromotion extends Model
{
    protected $fillable = [
        'business_id',
        'business_location_id',
        'name',
        'slug',
        'description',
        'regular_price',
        'promotion_price',
        'coupon_code',
        'qr_code_path',
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

    protected static function boot()
    {
        parent::boot();

        static::created(function ($promotion) {
            $promotion->generateQrCode();
        });

        static::updated(function ($promotion) {
            if ($promotion->isDirty('coupon_code') && $promotion->qr_code_path) {
                $promotion->regenerateQrCode();
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessPromotionImage::class, 'promotion_id')->orderBy('sort_order');
    }

    public function getFirstImageAttribute(): ?string
    {
        return $this->images->first()?->path;
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

    public function generateQrCode(): void
    {
        if (!$this->coupon_code) {
            return;
        }

        try {
            $slug = $this->business->slug ?? 'business-' . $this->business_id;
            $verifyUrl = url('/b/' . $slug . '/verify/' . $this->id . '/' . $this->coupon_code);

            $qrCode = new QrCode($verifyUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $directory = 'promotions/' . $this->business_id;
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $filename = 'qr_' . $this->id . '_' . time() . '.png';
            $path = $directory . '/' . $filename;

            Storage::disk('public')->put($path, $result->getString());

            $this->updateQuietly(['qr_code_path' => Storage::disk('public')->url($path)]);
        } catch (\Exception $e) {
            \Log::error('QR code generation failed: ' . $e->getMessage());
        }
    }

    public function regenerateQrCode(): void
    {
        if ($this->qr_code_path) {
            $oldPath = str_replace(url('/') . '/storage/', '', $this->qr_code_path);
            try {
                Storage::disk('public')->delete($oldPath);
            } catch (\Exception $e) {
                \Log::error('Failed to delete old QR: ' . $e->getMessage());
            }
        }
        $this->generateQrCode();
    }
}

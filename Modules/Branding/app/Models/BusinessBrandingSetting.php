<?php

namespace Modules\Branding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessBrandingSetting extends Model
{
    protected $fillable = [
        'business_id',
        'colors',
        'fonts',
        'custom_font_url',
        'dark_mode',
        'buttons_style',
        'generated_css',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'fonts' => 'array',
            'dark_mode' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public static function getDefaultColors(): array
    {
        return [
            'brand_primary' => ['light' => '#3B82F6', 'dark' => '#60A5FA'],
            'brand_secondary' => ['light' => '#8B5CF6', 'dark' => '#A78BFA'],
            'brand_tertiary' => ['light' => '#EC4899', 'dark' => '#F472B6'],
            'brand_quaternary' => ['light' => '#10B981', 'dark' => '#34D399'],
            'accent' => ['light' => '#F59E0B', 'dark' => '#FBBF24'],
            'hover' => ['light' => '#1F2937', 'dark' => '#374151'],
            'brand_link' => ['light' => '#3B82F6', 'dark' => '#60A5FA'],
            'brand_bgcolor_header' => ['light' => '#FFFFFF', 'dark' => '#1A1A2E'],
            'brand_bgcolor_footer' => ['light' => '#F8F9FA', 'dark' => '#16213E'],
        ];
    }

    public static function getDefaultFonts(): array
    {
        return [
            'heading' => 'Poppins',
            'body' => 'Open Sans',
            'buttons' => 'Poppins',
        ];
    }

    public function getColorsWithDefaults(): array
    {
        $defaults = self::getDefaultColors();
        $colors = $this->colors ?? [];

        foreach ($defaults as $key => $value) {
            if (!isset($colors[$key])) {
                $colors[$key] = $value;
            }
        }

        return $colors;
    }

    public function getFontsWithDefaults(): array
    {
        $defaults = self::getDefaultFonts();
        $fonts = $this->fonts ?? [];

        foreach ($defaults as $key => $value) {
            if (!isset($fonts[$key]) || empty($fonts[$key])) {
                $fonts[$key] = $value;
            }
        }

        return $fonts;
    }

    public function generateCss(): string
    {
        $colors = $this->getColorsWithDefaults();
        $fonts = $this->getFontsWithDefaults();
        $mode = $this->dark_mode ? 'dark' : 'light';

        $primary = $colors['brand_primary'][$mode] ?? '#3B82F6';
        $secondary = $colors['brand_secondary'][$mode] ?? '#8B5CF6';
        $tertiary = $colors['brand_tertiary'][$mode] ?? '#EC4899';
        $quaternary = $colors['brand_quaternary'][$mode] ?? '#10B981';
        $accent = $colors['accent'][$mode] ?? '#F59E0B';
        $hover = $colors['hover'][$mode] ?? '#1F2937';
        $brandLink = $colors['brand_link'][$mode] ?? '#3B82F6';
        $bgcolorHeader = $colors['brand_bgcolor_header'][$mode] ?? '#FFFFFF';
        $bgcolorFooter = $colors['brand_bgcolor_footer'][$mode] ?? '#F8F9FA';

        $fontHeading = $fonts['heading'] ?? 'Poppins';
        $fontBody = $fonts['body'] ?? 'Open Sans';
        $fontButtons = $fonts['buttons'] ?? 'Poppins';

        $buttonsStyle = match ($this->buttons_style) {
            'rounded' => '50px',
            'square' => '0px',
            'round' => '8px',
            default => '8px',
        };

        $css = ":root {
    --brand-primary: {$primary};
    --brand-secondary: {$secondary};
    --brand-tertiary: {$tertiary};
    --brand-quaternary: {$quaternary};
    --brand-accent: {$accent};
    --brand-hover: {$hover};
    --brand-link: {$brandLink};
    --brand-bgcolor-header: {$bgcolorHeader};
    --brand-bgcolor-footer: {$bgcolorFooter};
    --heading-font: '{$fontHeading}', sans-serif;
    --body-font: '{$fontBody}', sans-serif;
    --buttons-font: '{$fontButtons}', sans-serif;
    --buttons-border-radius: {$buttonsStyle};
}";

        $this->generated_css = $css;
        return $css;
    }
}

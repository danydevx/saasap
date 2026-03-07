<?php

namespace App\Services;

use App\Models\Setting;

class BrandingService
{
    public function forEmail(): array
    {
        $values = Setting::query()
            ->whereIn('key', [
                'branding.logo',
                'branding.primary_color',
                'branding.secondary_color',
                'branding.footer_text',
                'app.website',
                'app.email',
            ])
            ->pluck('value', 'key');

        return [
            'name' => config('app.name', 'SaaS'),
            'email' => $values->get('app.email') ?? config('mail.from.address'),
            'website' => $values->get('app.website') ?? config('app.url'),
            'logo' => $values->get('branding.logo'),
            'primary_color' => $values->get('branding.primary_color') ?? '#2563eb',
            'secondary_color' => $values->get('branding.secondary_color') ?? '#0f172a',
            'footer_text' => $values->get('branding.footer_text') ?? 'Gracias por confiar en nosotros.',
        ];
    }
}

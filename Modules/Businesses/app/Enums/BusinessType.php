<?php

namespace Modules\Businesses\Enums;

enum BusinessType: string
{
    case BARBER_SHOP = 'barber_shop';
    case BEAUTY_SALON = 'beauty_salon';
    case DENTIST = 'dentist';
    case MEDICAL_CLINIC = 'medical_clinic';
    case DOCTOR = 'doctor';
    case SPA = 'spa';
    case VETERINARIAN = 'veterinarian';
    case PHYSIOTHERAPIST = 'physiotherapist';
    case PSYCHOLOGIST = 'psychologist';
    case NUTRITIONIST = 'nutritionist';
    case TATTOO_STUDIO = 'tattoo_studio';
    case GENERIC = 'generic';

    public function label(): string
    {
        return match ($this) {
            self::BARBER_SHOP => 'Barber Shop',
            self::BEAUTY_SALON => 'Beauty Salon',
            self::DENTIST => 'Dentist',
            self::MEDICAL_CLINIC => 'Medical Clinic',
            self::DOCTOR => 'Doctor',
            self::SPA => 'Spa',
            self::VETERINARIAN => 'Veterinarian',
            self::PHYSIOTHERAPIST => 'Physiotherapist',
            self::PSYCHOLOGIST => 'Psychologist',
            self::NUTRITIONIST => 'Nutritionist',
            self::TATTOO_STUDIO => 'Tattoo Studio',
            self::GENERIC => 'Generic',
        };
    }
}

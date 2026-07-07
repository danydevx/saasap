<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MinisiteTheme;

class MinisiteThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Moderno',
                'slug' => 'modern',
                'description' => 'Estilo oscuro y sofisticado con acentos azules. Ideal para barberías, gyms y negocios modernos.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'primary' => '#1a1a2e',
                        'secondary' => '#16213e',
                        'accent' => '#0f3460',
                        'background' => '#ffffff',
                        'text' => '#1a1a2e',
                        'text_light' => '#6B7280',
                    ],
                    'fonts' => [
                        'headings' => 'Montserrat, sans-serif',
                        'body' => 'Inter, sans-serif',
                    ],
                    'border_radius' => '8px',
                    'card_style' => 'shadow-lg',
                    'button_style' => 'rounded-pill',
                ],
                'layout_config' => [
                    'page_style' => 'dark',
                    'section_style' => 'spacious',
                    'cards_per_row' => 3,
                    'hero_style' => 'fullwidth',
                    'animations' => [
                        'page_transition' => 'fade-in 0.5s ease-out',
                        'cards_hover' => 'lift: translateY(-8px)',
                        'buttons_hover' => 'scale(1.05)',
                        'sections' => 'fade-in-up 0.6s ease-out',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Elegante',
                'slug' => 'elegant',
                'description' => 'Estilo refinado con tonos dorados y tipografía serif. Perfecto para spas, salones de belleza y estudios de tatuajes premium.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'primary' => '#8B7355',
                        'secondary' => '#C9A86C',
                        'accent' => '#D4AF37',
                        'background' => '#FDF8F3',
                        'text' => '#2D2D2D',
                        'text_light' => '#7A7A7A',
                    ],
                    'fonts' => [
                        'headings' => 'Playfair Display, serif',
                        'body' => 'Lato, sans-serif',
                    ],
                    'border_radius' => '4px',
                    'card_style' => 'border-top-gold',
                    'button_style' => 'rounded-0',
                ],
                'layout_config' => [
                    'page_style' => 'light',
                    'section_style' => 'classic',
                    'cards_per_row' => 2,
                    'hero_style' => 'centered',
                    'animations' => [
                        'page_transition' => 'fade-in-up 0.6s ease-out',
                        'cards_hover' => 'glow: box-shadow 0 0 20px rgba(201, 168, 108, 0.4)',
                        'buttons_hover' => 'none',
                        'sections' => 'fade-in 0.8s ease-out',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Profesional',
                'slug' => 'professional',
                'description' => 'Diseño limpio y confiable con tonos verdes y azules. Ideal para profesionales de la salud, dentistas y consultorios.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'primary' => '#0D7377',
                        'secondary' => '#14A085',
                        'accent' => '#32E0C4',
                        'background' => '#F8FFFE',
                        'text' => '#212121',
                        'text_light' => '#757575',
                    ],
                    'fonts' => [
                        'headings' => 'Poppins, sans-serif',
                        'body' => 'Open Sans, sans-serif',
                    ],
                    'border_radius' => '12px',
                    'card_style' => 'border-start-primary',
                    'button_style' => 'rounded-8',
                ],
                'layout_config' => [
                    'page_style' => 'clean',
                    'section_style' => 'balanced',
                    'cards_per_row' => 3,
                    'hero_style' => 'split',
                    'animations' => [
                        'page_transition' => 'slide-in 0.4s ease-out',
                        'cards_hover' => 'border-expand: border-width 2px',
                        'buttons_hover' => 'color-shift',
                        'sections' => 'slide-up 0.5s ease-out',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Vintage',
                'slug' => 'vintage',
                'description' => 'Estilo rústico y acogedor con tonos café. Ideal para restaurantes, fondas y negocios con personalidad tradicional.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'primary' => '#5D4E37',
                        'secondary' => '#8B7355',
                        'accent' => '#C4A77D',
                        'background' => '#F5F0E8',
                        'text' => '#3D3D3D',
                        'text_light' => '#6B6B6B',
                    ],
                    'fonts' => [
                        'headings' => 'Playfair Display, serif',
                        'body' => 'Merriweather, serif',
                    ],
                    'border_radius' => '2px',
                    'card_style' => 'paper-texture',
                    'button_style' => 'rounded-0 border-2',
                ],
                'layout_config' => [
                    'page_style' => 'warm',
                    'section_style' => 'cozy',
                    'cards_per_row' => 2,
                    'hero_style' => 'boxed',
                    'animations' => [
                        'page_transition' => 'fade-in 0.8s ease-out',
                        'cards_hover' => 'paper-lift: translateY(-3px) rotate(0.5deg)',
                        'buttons_hover' => 'border-expand',
                        'sections' => 'fade-in 1s ease-out',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Friendly',
                'slug' => 'friendly',
                'description' => 'Diseño suave y amigable con tonos verdes. Perfecto para veterinarias y negocios orientado a mascotas.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'primary' => '#2E7D32',
                        'secondary' => '#81C784',
                        'accent' => '#A5D6A7',
                        'background' => '#F1F8E9',
                        'text' => '#1B5E20',
                        'text_light' => '#4CAF50',
                    ],
                    'fonts' => [
                        'headings' => 'Nunito, sans-serif',
                        'body' => 'Quicksand, sans-serif',
                    ],
                    'border_radius' => '16px',
                    'card_style' => 'rounded-xl shadow-sm',
                    'button_style' => 'rounded-20',
                ],
                'layout_config' => [
                    'page_style' => 'fresh',
                    'section_style' => 'rounded',
                    'cards_per_row' => 4,
                    'hero_style' => 'friendly',
                    'animations' => [
                        'page_transition' => 'bounce-in 0.5s ease-out',
                        'cards_hover' => 'bounce: translateY(-4px)',
                        'buttons_hover' => 'wiggle: rotate(3deg)',
                        'sections' => 'bounce-in 0.6s ease-out',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Bold',
                'slug' => 'bold',
                'description' => 'Diseño impactante con colores fuertes y alto contraste. Para negocios que quieren destacar con personalidad única.',
                'preview_image' => null,
                'css_variables' => [
                    'colors' => [
                        'primary' => '#FF4500',
                        'secondary' => '#FF6B35',
                        'accent' => '#FFD700',
                        'background' => '#1A1A1A',
                        'text' => '#FFFFFF',
                        'text_light' => '#B0B0B0',
                    ],
                    'fonts' => [
                        'headings' => 'Oswald, sans-serif',
                        'body' => 'Roboto Condensed, sans-serif',
                    ],
                    'border_radius' => '0px',
                    'card_style' => 'sharp-corners border-accent',
                    'button_style' => 'rounded-0 uppercase',
                ],
                'layout_config' => [
                    'page_style' => 'dark',
                    'section_style' => 'dramatic',
                    'cards_per_row' => 3,
                    'hero_style' => 'fullbleed',
                    'animations' => [
                        'page_transition' => 'zoom-in 0.4s ease-out',
                        'cards_hover' => 'neon-glow: box-shadow 0 0 30px rgba(255, 69, 0, 0.5)',
                        'buttons_hover' => 'fill-in: background-color transition',
                        'sections' => 'zoom-in 0.5s ease-out',
                    ],
                ],
                'section_config' => [
                    'section_order' => ['hero', 'services', 'gallery', 'products', 'appointments', 'contact', 'reviews', 'locations', 'promotions', 'footer'],
                    'section_props' => [
                        'hero' => [
                            'layout' => 'fullbleed',
                            'alignment' => 'left',
                            'showLogo' => true,
                            'showContactInfo' => true,
                            'showSocialLinks' => false,
                            'overlayOpacity' => 0.7,
                            'backgroundType' => 'gradient',
                            'gradientStart' => '#FF4500',
                            'gradientEnd' => '#FF6B35',
                        ],
                        'services' => [
                            'showTitle' => true,
                            'title' => 'Nuestros Servicios',
                            'subtitle' => '',
                            'showBookingButton' => true,
                            'showWhatsApp' => true,
                            'titleColor' => '#1a1a2e',
                            'subtitleColor' => '#6B7280',
                            'sectionBgColor' => '#ffffff',
                            'cardStyle' => 'light',
                            'buttonStyle' => 'primary',
                        ],
                        'contact' => [
                            'showTitle' => true,
                            'title' => 'Contáctanos',
                            'subtitle' => '',
                            'titleColor' => '#1a1a2e',
                            'subtitleColor' => '#6B7280',
                            'sectionBgColor' => '#f8f9fa',
                            'showSocial' => true,
                            'showContactInfo' => true,
                            'showLocations' => true,
                            'accentColor' => '#FF4500',
                        ],
                        'footer' => [
                            'showBrand' => true,
                            'showContact' => true,
                            'showSocial' => true,
                            'showLinks' => false,
                            'backgroundColor' => '#1a1a2e',
                            'textColor' => '#ffffff',
                            'accentColor' => '#FF4500',
                            'linkColor' => '#b0b0b0',
                        ],
                    ],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($themes as $theme) {
            MinisiteTheme::updateOrCreate(
                ['slug' => $theme['slug']],
                $theme
            );
        }
    }
}

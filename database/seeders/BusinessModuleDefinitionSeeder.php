<?php

namespace Database\Seeders;

use App\Models\BusinessModuleDefinition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessModuleDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'key' => 'locations',
                'name' => 'Ubicaciones',
                'description' => 'Gestiona multiple ubicaciones o direcciones para tu negocio',
                'icon' => 'bi bi-geo-alt',
                'sort_order' => 1,
                'has_settings' => false,
            ],
            [
                'key' => 'contact_form',
                'name' => 'Formulario de Contacto',
                'description' => 'Formulario de contacto publico para recibir mensajes',
                'icon' => 'bi bi-envelope',
                'sort_order' => 2,
                'has_settings' => false,
            ],
            [
                'key' => 'gallery',
                'name' => 'Galeria',
                'description' => 'Galeria de imagenes para mostrar tus productos o servicios',
                'icon' => 'bi bi-images',
                'sort_order' => 3,
                'has_settings' => false,
            ],
            [
                'key' => 'leads',
                'name' => 'Leads',
                'description' => 'Captura y gestion de prospectos de clientes',
                'icon' => 'bi bi-person-plus',
                'sort_order' => 4,
                'has_settings' => false,
            ],
            [
                'key' => 'services',
                'name' => 'Servicios',
                'description' => 'Gestiona los servicios que ofreces',
                'icon' => 'bi bi-briefcase',
                'sort_order' => 5,
                'has_settings' => false,
            ],
            [
                'key' => 'appointments',
                'name' => 'Citas y Reservas',
                'description' => 'Sistema de citas y reservas en linea',
                'icon' => 'bi bi-calendar-check',
                'sort_order' => 6,
                'has_settings' => true,
            ],
            [
                'key' => 'products',
                'name' => 'Productos',
                'description' => 'Gestion de productos en venta',
                'icon' => 'bi bi-box-seam',
                'sort_order' => 7,
                'has_settings' => false,
            ],
            [
                'key' => 'ai_chatbot',
                'name' => 'AI Chatbot',
                'description' => 'Asistente virtual con IA para atender clientes',
                'icon' => 'bi bi-robot',
                'sort_order' => 8,
                'has_settings' => true,
            ],
            [
                'key' => 'reviews',
                'name' => 'Reviews',
                'description' => 'Customer reviews and testimonials',
                'icon' => 'bi bi-star',
                'sort_order' => 9,
                'has_settings' => false,
            ],
            [
                'key' => 'promotions',
                'name' => 'Promotions',
                'description' => 'Manage promotions, deals and coupons',
                'icon' => 'bi bi-tag',
                'sort_order' => 10,
                'has_settings' => false,
            ],
            [
                'key' => 'restaurant_menu',
                'name' => 'Menú de Restaurante',
                'description' => 'Menu digital para restaurantes con categorias, productos y variantes',
                'icon' => 'bi bi-list-ul',
                'sort_order' => 11,
                'has_settings' => false,
            ],
            [
                'key' => 'about',
                'name' => 'Acerca de',
                'description' => 'Sección Acerca de para tu negocio',
                'icon' => 'bi bi-info-circle',
                'sort_order' => 12,
                'has_settings' => false,
            ],
            [
                'key' => 'socialmedia',
                'name' => 'Redes Sociales',
                'description' => 'Gestiona tus redes sociales',
                'icon' => 'bi bi-share',
                'sort_order' => 13,
                'has_settings' => false,
            ],
            [
                'key' => 'features',
                'name' => 'Caracteristicas',
                'description' => 'Amenidades y caracteristicas de tu negocio (wifi, estacionamiento, etc)',
                'icon' => 'bi bi-check-circle',
                'sort_order' => 14,
                'has_settings' => false,
            ],
        ];

        foreach ($modules as $module) {
            BusinessModuleDefinition::updateOrCreate(
                ['key' => $module['key']],
                $module
            );
        }

        $this->command->info('Business module definitions seeded: ' . count($modules) . ' modules');
    }
}

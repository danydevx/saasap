<?php

namespace Modules\AiChatbot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'AiChatbot';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
    }
}

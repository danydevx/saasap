<?php

namespace App\Http\Middleware;

use App\Services\FeatureService;
use App\Services\ModuleService;
use App\Services\SystemAnnouncementService;
use App\Services\TemplateRenderService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $featureService = app(FeatureService::class);
        $announcements = null;
        if ($user) {
            $templates = app(TemplateRenderService::class);
            $variables = [
                'user_name' => (string) ($user->name ?? ''),
                'user_email' => (string) ($user->email ?? ''),
                'app_name' => (string) config('app.name'),
                'support_email' => (string) (config('mail.from.address') ?? ''),
                'date' => now()->toDateString(),
            ];

            $announcements = app(SystemAnnouncementService::class)
                ->activeForUser($user)
                ->map(fn ($announcement) => [
                    'id' => $announcement->id,
                    'title' => $templates->renderText($announcement->title, $variables),
                    'message' => $templates->renderText($announcement->message, $variables),
                    'type' => $announcement->type,
                    'priority' => $announcement->priority,
                    'dismissible' => $announcement->dismissible,
                    'action_label' => $announcement->action_label,
                    'action_url' => $announcement->action_url,
                ])
                ->values();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'permissions' => $user
                    ? $user->getAllPermissions()->pluck('name')->values()
                    : [],
                'roles' => $user
                    ? $user->getRoleNames()->values()
                    : [],
            ],
            'notificationUnreadCount' => $user
                ? $user->notifications()->where('is_read', false)->count()
                : 0,
            'features' => $user ? $featureService->allForUser($user) : [],
            'modules' => app(ModuleService::class)->forFrontend(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'systemAnnouncements' => $announcements,
        ];
    }
}

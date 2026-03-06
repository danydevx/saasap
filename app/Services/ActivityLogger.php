<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function log(string $event, array $options = []): ActivityLog
    {
        $user = $options['user'] ?? null;
        $actor = $options['actor'] ?? null;
        $subject = $options['subject'] ?? null;
        $description = $options['description'] ?? null;
        $metadata = $options['metadata'] ?? null;
        $request = $options['request'] ?? null;

        return ActivityLog::create([
            'user_id' => $this->idFrom($user),
            'actor_id' => $this->idFrom($actor),
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $this->ipFrom($request),
            'user_agent' => $this->userAgentFrom($request),
            'metadata' => $metadata,
        ]);
    }

    private function idFrom(?User $user): ?int
    {
        return $user?->getKey();
    }

    private function ipFrom(?Request $request): ?string
    {
        return $request?->ip();
    }

    private function userAgentFrom(?Request $request): ?string
    {
        return $request?->userAgent();
    }
}

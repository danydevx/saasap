<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPasswordResetEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public string $token,
        public string $code
    ) {}

    public function handle(): void
    {
        $user = User::query()->find($this->userId);
        if (! $user) {
            return;
        }

        $user->notify(new PasswordResetCodeNotification($this->token, $this->code));
    }
}

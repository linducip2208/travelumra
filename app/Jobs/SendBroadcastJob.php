<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBroadcastJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $broadcastId) {}

    public function handle(NotificationService $notificationService): void
    {
        $broadcast = Broadcast::find($this->broadcastId);
        if (! $broadcast) {
            return;
        }
        $notificationService->send($broadcast);
    }
}

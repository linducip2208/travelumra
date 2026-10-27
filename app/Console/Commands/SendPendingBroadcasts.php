<?php

namespace App\Console\Commands;

use App\Enums\BroadcastStatus;
use App\Jobs\SendBroadcastJob;
use App\Models\Broadcast;
use Illuminate\Console\Command;

class SendPendingBroadcasts extends Command
{
    protected $signature = 'broadcasts:send';

    protected $description = 'Kirim semua siaran terjadwal yang waktunya sudah tiba';

    public function handle(): void
    {
        $broadcasts = Broadcast::where('status', BroadcastStatus::Scheduled)
            ->where('scheduled_at', '<=', now())
            ->get(['id', 'title']);

        if ($broadcasts->isEmpty()) {
            $this->info('Tidak ada siaran yang perlu dikirim.');

            return;
        }

        $this->info("Ditemukan {$broadcasts->count()} siaran, dispatch ke queue...");

        foreach ($broadcasts as $broadcast) {
            SendBroadcastJob::dispatch($broadcast->id)->onQueue('broadcasts');
            $this->line("Queued: {$broadcast->title} (ID: {$broadcast->id})");
        }

        $this->info('Selesai dispatch.');
    }
}

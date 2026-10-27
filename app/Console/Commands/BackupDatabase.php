<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup database menggunakan mysqldump';

    public function handle(): void
    {
        $db = config('database.connections.mysql');

        $filename = 'backup-' . now()->format('Y-m-d_His') . '.sql';
        $dir = storage_path('backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['host']),
            (int) ($db['port'] ?? 3306),
            escapeshellarg($db['database']),
            escapeshellarg($path)
        );

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode === 0) {
            $this->info("Backup sukses: {$filename}");
            \Log::info('db:backup sukses', ['file' => $filename]);
        } else {
            $this->error("Backup gagal (exit code: {$exitCode})");
            \Log::error('db:backup gagal', [
                'file' => $filename,
                'exit_code' => $exitCode,
                'output' => $output,
            ]);
        }
    }
}

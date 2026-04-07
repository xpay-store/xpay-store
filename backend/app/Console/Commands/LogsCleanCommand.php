<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class LogsCleanCommand extends Command
{
    protected $signature = 'logs:clean {--days=30 : Delete log files older than this many days}';

    protected $description = 'Remove rotated Laravel log files older than the retention window.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = 30;
        }

        $path = storage_path('logs');
        if (! File::isDirectory($path)) {
            return self::SUCCESS;
        }

        $cutoff = Carbon::now()->subDays($days)->timestamp;
        $deleted = 0;

        foreach (File::files($path) as $file) {
            $mtime = @filemtime($file->getPathname());
            if ($mtime !== false && $mtime < $cutoff) {
                @unlink($file->getPathname());
                $deleted++;
            }
        }

        $this->info('Deleted old log files: '.$deleted);

        return self::SUCCESS;
    }
}

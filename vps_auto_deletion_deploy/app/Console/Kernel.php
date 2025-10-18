<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto tap-out all active members at midnight (gym closing time)
        $schedule->command('rfid:auto-tapout')
                 ->dailyAt('00:00')
                 ->timezone('Asia/Manila')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Process inactive member deletions daily
        $schedule->command('members:process-inactive-deletion')
                 ->dailyAt('02:00') // Run at 2 AM by default
                 ->timezone('Asia/Manila')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->when(function () {
                     // Only run if auto-deletion is enabled
                     $settings = \App\Models\AutoDeletionSettings::first();
                     return $settings && $settings->is_enabled;
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

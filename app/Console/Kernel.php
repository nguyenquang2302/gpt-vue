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
        $schedule->command('bank:MBCheck')
                ->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('banklogs:check')->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('posback:check')->everyFifteenMinutes()->withoutOverlapping();

        // $schedule->command('inspire')->hourly();
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

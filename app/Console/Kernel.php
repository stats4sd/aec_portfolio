<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // from upgrade to Laravel 10 (not sure if this is needed, but doesn't hurt)
        $schedule->command('cache:prune-stale-tags')->hourly();

        $schedule->command('senddataremovalreminderemail')->dailyAt('00:11');

        $schedule->command('app:get-one-day-exchange-rates')->dailyAt('06:00');

        $schedule->command('app:remove-old-pdf-prints')->weeklyOn(Schedule::SUNDAY, '01:00');

        $schedule->command('media-library:delete-old-temporary-uploads')->daily();

        $schedule->command('app:write-log-message')->dailyAt('14:06');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

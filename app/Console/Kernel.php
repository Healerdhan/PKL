<?php

namespace App\Console;

use App\Console\Commands\CreateDatabase;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        // $this->app['Illuminate\Contracts\Console\Kernel']->command('create:database', function () {
        //     $this->call(CreateDatabase::class);
        // });

        require base_path('routes/console.php');
    }
}

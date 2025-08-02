<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\sendPayNotify;
use App\Console\Commands\CheckOutstandingPayments;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
     protected $commands = [
         sendPayNotify::class,
         CheckOutstandingPayments::class
         ];
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
         $schedule->command('send:notify')->daily();
          $schedule->command('payments:check')->dailyAt('08:00');
         $schedule->command('queue:work --daemon  --once --stop-when-empty --memory=128')->withoutOverlapping(2);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

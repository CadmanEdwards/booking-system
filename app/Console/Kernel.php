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
       /*'App\Console\Commands\Createemployeschedule',*/
        'App\Console\Commands\ImapEmailClient',
        'App\Console\Commands\ReminderEmailClient',
        'App\Console\Commands\ReminderEmailInvoice',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reminder:email')
                  ->daily()->sendOutputTo('laravel.log');
                  /*
                  $schedule->command('reminder:email')
                  ->everyMinute()->sendOutputTo('laravel.log');*/
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

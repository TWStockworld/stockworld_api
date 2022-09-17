<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\UpdateStockData;
use App\Jobs\UpdateStockInformation;


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
        // $schedule->call(function () {
        //     $controller = new \App\Http\Controllers\Controller();
        //     $controller->sendmail();
        // })->everyMinute();
        $schedule->job(new UpdateStockInformation)->dailyAt('13:50');
        $input = date_format(now(), "Y-m-d");
        $schedule->job(new UpdateStockData($input))->dailyAt('14:00');
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

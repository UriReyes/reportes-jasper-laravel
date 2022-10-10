<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;
use Stringable;

class Kernel extends ConsoleKernel
{

    protected $command = [
        Commands\TaskGeneraInformes::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (!Storage::get('cron/time.txt')) {
            Storage::makeDirectory('cron');
            Storage::put('cron/time.txt', '00:00');
            $time = Storage::get('cron/time.txt');
        } else {
            $time = Storage::get('cron/time.txt');
        }

        $schedule->command('GeneraInformes:task')->timezone('America/Mexico_City')->monthlyOn(1, $time)
            ->onFailure(function (Stringable $output) {
                // The task failed...
                $texto = "[" . date("Y-m-d H:i:s") . "]: " . $output;
                Storage::append("tareas_programadas.txt", $texto);
            });
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

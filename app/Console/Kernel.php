<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;


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
        $schedule->command('GeneraInformes:task')->timezone('America/Mexico_City')->monthlyOn(17, '14:41')
        
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
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

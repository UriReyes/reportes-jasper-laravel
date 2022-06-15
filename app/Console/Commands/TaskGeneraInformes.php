<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\ExportAllReports;

class TaskGeneraInformes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GeneraInformes:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera informes mensuales masivos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $texto = "[" . date("Y-m-d H:i:s") . "]: Inicio de tarea de generación de informes.";
        // Storage::append("tareas_programadas.txt", $texto);
        $reportes = new ExportAllReports();
        $reportes->startProcess();
        // $texto = "[" . date("Y-m-d H:i:s") . "]: Fin de tarea de generación de informes.";
        // Storage::append("tareas_programadas.txt", $texto);
        //return 0;
    }
}

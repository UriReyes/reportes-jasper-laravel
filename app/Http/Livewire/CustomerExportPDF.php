<?php

namespace App\Http\Livewire;

use App\Events\DownloadInformationAPI;
use App\Events\ProcessReport;
use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Jenssegers\Date\Date;

class CustomerExportPDF extends Component implements ShouldBroadcast
{
    use ApiSite24x7, ReestructurarDatosAPISite24x7, GenerarReportesSite24x7;
    use LivewireAlert;
    public $customer;
    public $totalMonitors;
    public $zaaid;
    public $name;
    public $last_month;
    public $percentage = 0;
    public $completed_reports = 0;
    public $period_report;
    public $start_custom_date;
    public $end_custom_date;
    public $storedInformation;


    protected $listeners = ['completedExport', 'reloadProcessExport' => 'startProcess'];

    public function broadcastOn()
    {

        return new Channel(env('PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL'));
    }

    public function mount($customer, $period, $last_month)
    {
        $this->customer = $customer;
        $this->zaaid = $customer['zaaid'];
        $this->name = $customer['name'];
        $this->period_report = $period;
        $this->last_month = $last_month;
    }

    public function render()
    {

        return view('livewire.customer-export-p-d-f');
    }

    public function startProcess()
    {

        Storage::makeDirectory('token');
        Storage::makeDirectory('inicio_procesos');
        Storage::put('inicio_procesos/InicioByMPS.txt', now()->format('d-m-Y H:i:s'));
        $refresh_token = $this->getRefreshToken()->access_token;
        Storage::put('token/refreshTokenByMSP.txt', $refresh_token);
        if (!$this->period_report || !in_array($this->period_report, [7, 13, 25, 50])) {
            $this->alert('info', '¡Debes seleccionar un periodo válido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        } else {
            if ($this->period_report == 50 && (!$this->start_custom_date || !$this->end_custom_date)) {
                $this->alert('info', '¡Debes seleccionar una fecha de inicio y una fecha de fin!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
            } else {
                Storage::makeDirectory('/public/state-msp/');
                Storage::makeDirectory('public/downloaded_msp_information');
                $completed_monitors = [];
                $totalMonitorsMask = 1;
                $state = json_decode(Storage::get('public/state-msp/state.json'), true);
                $this->storedInformation = [];
                array_push($this->storedInformation,  [
                    'id' => null,
                    'name' => $this->name,
                    'zaaid' => $this->zaaid,
                    'monitors' => []
                ]);

                $site24x7Url = env('SITE_24X7_API');
                $r_token = Storage::get('token/refreshTokenByMSP.txt');
                // $refresh_token = $this->getRefreshToken();
                $refresh_token = $r_token;
                if ($refresh_token == 500) {
                    $this->alert('info', 'Upps!', [
                        'position' => 'center',
                        'timer' => 5000,
                        'toast' => true,
                        'text' => 'Ocurrió un error de comunicación con el API, espere un momento y vuelva a intentarlo',
                    ]);
                } else {
                    $monitors = $this->getMonitors($site24x7Url, $this->zaaid, $refresh_token);
                    $totalMonitorsMask = count($monitors);
                    if ($state != null) {
                        if (array_key_exists('zaaid', $state)) {
                            if ($this->zaaid == $state['zaaid']) {
                                $this->completed_reports = $state['completed_reports'];
                                $this->percentage = $state['percentage'];
                                $completed_monitors_decode = json_decode($state['completed_monitors']);
                                $monitors = array_filter($monitors, function ($monitor_item) use ($completed_monitors_decode) {
                                    return !in_array($monitor_item['monitor_id'], $completed_monitors_decode);
                                });
                                $completed_monitors = $completed_monitors_decode;
                                $getDownloadInformation = json_decode(Storage::get("public/downloaded_msp_information/{$this->name}_{$this->zaaid}.json"), true);
                                if ($getDownloadInformation != null) {
                                    $this->storedInformation[0]['monitors'] = $getDownloadInformation[0]['monitors'];
                                }
                            }
                        }
                    }

                    if ($monitors == 'error') {
                        $this->alert('info', 'Upps!', [
                            'position' => 'center',
                            'timer' => 5000,
                            'toast' => true,
                            'text' => 'Ocurrió un error de comunicación con el API, espere un momento y vuelva a intentarlo',
                        ]);
                    } else {
                        $this->totalMonitors = count($monitors);
                        $this->completed_reports = count($completed_monitors);
                        $this->percentage = $totalMonitorsMask > 0 ? ($this->getPercentage($this->completed_reports, $totalMonitorsMask)) : 0;
                        $start_time = microtime(true);

                        if ($this->totalMonitors > 1000) {
                            $chunk_monitors = array_chunk($monitors, 1000, true);
                            $totalChunks = count($chunk_monitors);
                            $count_chunk = 1;
                            foreach ($chunk_monitors as $monitors) {
                                $r_token = Storage::get('token/refreshTokenByMSP.txt');
                                // $refresh_token = $this->getRefreshToken();
                                $refresh_token = $r_token;
                                $this->iterateMonitors($monitors, $completed_monitors, $totalMonitorsMask, $start_time, $site24x7Url, $refresh_token, $totalChunks, $count_chunk);
                                $count_chunk++;
                                sleep(60);
                            }
                        } else {
                            $monitorsCollect = collect();
                            $this->iterateMonitors($monitors, $completed_monitors, $totalMonitorsMask, $start_time, $site24x7Url, $refresh_token);
                        }

                        // Se realiza el proceso de generacion de reportes


                        // $this->generateMSPReport("downloaded_msp_information/{$this->name}_{$this->zaaid}.json");
                    }
                }
            }
        }
    }

    public function iterateMonitors($monitors, $completed_monitors, $totalMonitorsMask, $start_time, $site24x7Url, $refresh_token, $totalChunks = null, $countChunk = null)
    {
        foreach ($monitors as $monitor) {
            // $finish_time = microtime(true);
            // $time = $finish_time - $start_time;
            // if ($time > 3500) {
            //     $r_token = Storage::get('token/refreshTokenByMSP.txt');
            //     // $refresh_token = $this->getRefreshToken();
            //     $refresh_token = $r_token;
            //     $start_time = microtime(true);
            // }
            $inicio_proceso = Storage::get('inicio_procesos/InicioByMPS.txt');
            if (Carbon::parse($inicio_proceso)->diffInMinutes(now()) > 55) {
                $refresh_token = $this->getRefreshToken()->access_token;
                Storage::put('token/refreshTokenByMSP.txt', $refresh_token);
                Storage::put('inicio_procesos/InicioByMPS.txt', now()->format('d-m-Y H:i:s'));
            }
            //if ($monitor['monitor_id'] == '417536000002177252') {
            $processedMonitor = $this->processSite24x7Monitors(
                $monitor,
                $site24x7Url,
                $refresh_token,
                $this->zaaid,
                $this->name,
                $this->last_month
            );

            $this->completed_reports++;
            $this->percentage = $this->getPercentage($this->completed_reports, $totalMonitorsMask);
            array_push($this->storedInformation[0]['monitors'], $processedMonitor);
            file_put_contents(public_path("storage/downloaded_msp_information/{$this->name}_{$this->zaaid}.json"), json_encode($this->storedInformation));
            //SAVE STATE
            array_push($completed_monitors, $monitor['monitor_id']);
            $state_stored = json_encode([
                'customer' => $this->name,
                'zaaid' => $this->zaaid,
                'completed_monitors' => json_encode($completed_monitors),
                'completed_reports' => $this->completed_reports,
                'percentage' => $this->percentage,
                'downloaded_files' => false
            ], JSON_PRETTY_PRINT);
            Storage::put('public/state-msp/state.json', $state_stored);
            $message = "Descargando Información...";
            if ($totalChunks) {
                $message = "Fase {$countChunk} de {$totalChunks} | Descargando...";
            }
            event(new DownloadInformationAPI($totalMonitorsMask, $this->percentage, $this->completed_reports, $this->zaaid, $this->name, $message));
        }
    }

    public function completedExport($data)
    {
        $this->alert('success', 'Bien Hecho', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
            'text' => "Se han exportado {$data->total_monitors} monitores de {$data->customer} correctamente",
        ]);
    }
}

<?php

namespace App\Http\Livewire;

use App\Events\DownloadInformationAPI;
use App\Events\ProcessReport;
use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
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
    public $storedInformation;

    protected $listeners = ['completedExport', 'reloadProcessExport' => 'startProcess'];

    public function broadcastOn()
    {
        return new Channel('progress-reportD');
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
        $refresh_token = $this->getRefreshToken();
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
                // $start_time = microtime(true);

                $monitorsCollect = collect();
                // Se realiza el proceso de generacion de reportes
                foreach ($monitors as $monitor) {
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
                    event(new DownloadInformationAPI($totalMonitorsMask, $this->percentage, $this->completed_reports, $this->zaaid, $this->name, 'Descargando Información...'));
                }

                $this->generateMSPReport("downloaded_msp_information/{$this->name}_{$this->zaaid}.json");
            }
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

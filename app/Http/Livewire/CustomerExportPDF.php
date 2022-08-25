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

    protected $listeners = ['completedExport'];

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

        Storage::makeDirectory('public/downloaded_msp_information');
        $this->storedInformation = collect();
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
            if ($monitors == 'error') {
                $this->alert('info', 'Upps!', [
                    'position' => 'center',
                    'timer' => 5000,
                    'toast' => true,
                    'text' => 'Ocurrió un error de comunicación con el API, espere un momento y vuelva a intentarlo',
                ]);
            } else {
                $this->totalMonitors = count($monitors);
                $this->completed_reports = 0;
                $this->percentage = 0;
                $start_time = microtime(true);

                $monitorsCollect = collect();
                // Se realiza el proceso de generacion de reportes
                foreach ($monitors as $monitor) {
                    //if ($monitor['monitor_id'] == '417536000002177252') {
                    $processedMonitor = $this->processSite24x7Monitors($monitor, $site24x7Url, $refresh_token, $this->zaaid, $this->name, $this->last_month);
                    $this->completed_reports++;
                    $this->percentage = $this->getPercentage($this->completed_reports, $this->totalMonitors);
                    event(new DownloadInformationAPI($this->totalMonitors, $this->percentage, $this->completed_reports, $this->zaaid, $this->name));

                    $finish_time = microtime(true);
                    $time = $finish_time - $start_time;
                    if ($time > 3500) {
                        $refresh_token = $this->getRefreshToken();
                        $start_time = microtime(true);
                    }
                    $monitorsCollect->push($processedMonitor);
                    //}
                }
                $this->storedInformation->push([
                    'id' => null,
                    'name' => $this->name,
                    'zaaid' => $this->zaaid,
                    'monitors' => $monitorsCollect
                ]);
                file_put_contents(public_path("storage/downloaded_msp_information/{$this->name}_{$this->zaaid}.json"), json_encode($this->storedInformation));
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

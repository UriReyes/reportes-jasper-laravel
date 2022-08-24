<?php

namespace App\Http\Livewire;

use App\Events\ProcessReport;
use App\Events\ProcessReports;
use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Livewire\Component;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ExportAllReports extends Component
{
    use ApiSite24x7, ReestructurarDatosAPISite24x7, GenerarReportesSite24x7;
    use LivewireAlert;

    public $period = 7;
    public $last_month;
    public $totalMonitors;
    public $percentage = 0;
    public $completed_reports = 0;
    public $percentage_customers = 0;
    public $completed_customers = 0;
    public $period_report = 7;
    public $storedInformation;

    public function mount()
    {
        Date::setLocale('es');
        $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
    }

    public function render()
    {
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();
        if ($refresh_token == 'access_denied') {
            $customers_its = [];
        } else {
            $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
            if ($customers_its == 401) {
                $customers_its = [];
            }
        }
        return view('livewire.export-all-reports', compact('customers_its'));
    }

    public function startProcess()
    {
        Storage::makeDirectory('downloaded_msp_information');
        Date::setLocale('es');
        $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));

        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();

        $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
        if ($customers_its == 401) {
            $this->alert('info', 'Upps!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Ocurrió un error de comunicación con el API, espere un momento y vuelva a intentarlo',
            ]);
        } else {
            $start_time = microtime(true);
            $texto = "[" . date("Y-m-d H:i:s") . "]: Inicio de tarea de generación de informes.";
            Storage::append("tareas_programadas.txt", $texto);
            foreach ($customers_its as $customer_it) {
                $this->storedInformation = collect();
                $customer_id = $customer_it['user_id'];
                $customer = $customer_it['name'];
                $zaaid = $customer_it['zaaid'];
                $monitors = $this->getMonitors($site24x7Url, $zaaid, $refresh_token);
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

                    $monitorsCollect = collect();
                    foreach ($monitors as $monitor) {
                        $processedMonitor = $this->processSite24x7Monitors($monitor, $site24x7Url, $refresh_token, $zaaid, $customer, $this->last_month);
                        $this->completed_reports++;
                        $this->percentage = $this->getPercentage($this->completed_reports, $this->totalMonitors);
                        event(new ProcessReport($this->totalMonitors, $this->percentage, $this->completed_reports, $zaaid, $customer));
                        $finish_time = microtime(true);
                        $time = $finish_time - $start_time;
                        if ($time > 3500) {
                            $refresh_token = $this->getRefreshToken();
                            $start_time = microtime(true);
                        }
                        $monitorsCollect->push($processedMonitor);
                    }

                    $this->storedInformation->push([
                        'id' => $customer_id,
                        'name' => $customer,
                        'zaaid' => $zaaid,
                        'monitors' => $monitorsCollect
                    ]);
                    file_put_contents(public_path("storage/downloaded_msp_information/{$customer}_{$zaaid}.json"), json_encode($this->storedInformation));
                    $this->completed_customers++;
                    $this->percentage_customers = $this->getPercentage($this->completed_customers, count($customers_its));
                    event(new ProcessReports(count($customers_its), $this->percentage_customers, $this->completed_customers, $customer_id));

                    $texto = "[" . date("Y-m-d H:i:s") . "]: " . $customer;
                    Storage::append("tareas_programadas.txt", $texto);
                }
            }
            sleep(5);
            $this->generateMSPReports();
            $texto = "[" . date("Y-m-d H:i:s") . "]: Fin de tarea de generación de informes.";
            Storage::append("tareas_programadas.txt", $texto);
        }
    }
}

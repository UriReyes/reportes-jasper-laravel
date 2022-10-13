<?php

namespace App\Http\Livewire;

use App\Events\DownloadAllInformationAPI;
use App\Events\DownloadInformationAPI;
use App\Events\ProcessReport;
use App\Events\ProcessReports;
use App\Mail\GeneracionDeReporteFinalizada;
use App\Mail\GeneracionDeReportesFinalizada;
use App\Mail\GeneracionDeReportesIniciada;
use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Livewire\Component;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ExportAllReports extends Component
{
    use ApiSite24x7, ReestructurarDatosAPISite24x7, GenerarReportesSite24x7;
    use LivewireAlert;

    // public $period = 7;
    public $showStartEndDate = false;
    public $last_month;
    public $start_custom_date;
    public $end_custom_date;
    public $totalMonitors;
    public $percentage = 0;
    public $completed_reports = 0;
    public $percentage_customers = 0;
    public $completed_customers = 0;
    public $period_report = 7;
    public $storedInformation;
    public $msp_init = 'undefinedMSP';

    // protected $listeners = ['reloadProcessExportAll' => 'startProcess'];
    protected $queryString = [
        'last_month' => ['except' => ''],
        'period_report' => ['except' => ''],
        'showStartEndDate' => ['except' => ''],
        'start_custom_date' => ['except' => ''],
        'end_custom_date' => ['except' => ''],
    ];
    public function mount()
    {
        Date::setLocale('es');
        if (!$this->last_month) {
            $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
        }
    }


    public function updatedPeriodReport($value)
    {
        Date::setLocale('es');
        $this->period_report = $value;
        if ($value == "") {
            $this->showStartEndDate = false;
            $this->resetStartEndDates();
            $this->last_month = null;
        }
        if ($value == 7) {
            $this->showStartEndDate = false;
            $this->resetStartEndDates();
            $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
        } else if ($value == 13) {
            $this->showStartEndDate = false;
            $this->resetStartEndDates();
            $this->last_month = ucfirst(Date::now()->format('F'));
        } else if ($value == 25) {
            $this->showStartEndDate = false;
            $this->resetStartEndDates();
            $this->last_month = "Del 1 de " . ucfirst(Date::now()->subMonths(3)->format('F')) . " al 1 de " . ucfirst(Date::now()->format('F'));
        } else if ($value == 50) {
            $this->showStartEndDate = true;
            $this->last_month = "Del {$this->start_custom_date} al {$this->end_custom_date}";
        }
    }

    public function updatedStartCustomDate()
    {
        $this->last_month = "Del {$this->start_custom_date} al {$this->end_custom_date}";
    }
    public function updatedEndCustomDate()
    {
        $this->last_month = "Del {$this->start_custom_date} al {$this->end_custom_date}";
    }

    public function resetStartEndDates()
    {
        $this->start_custom_date = null;
        $this->end_custom_date = null;
    }

    public function render()
    {
        $site24x7Url = env('SITE_24X7_API');
        Storage::makeDirectory('token');
        $refresh_token = $this->getRefreshToken()->access_token;
        Storage::put('token/refreshToken.txt', $refresh_token);
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

    public function startProcess($tipo = null)
    {
        if (Storage::get('email/email.txt')) {
            Mail::to(Storage::get('email/email.txt'))->send(new GeneracionDeReportesIniciada());
        }
        Storage::put('inicio_procesos/InicioMasiveExport.txt', now()->format('d-m-Y H:i:s'));
        if ($tipo == 'cron') {
            Storage::makeDirectory('token');
            $refresh_token_generate = $this->getRefreshToken()->access_token;
            Storage::put('token/refreshToken.txt', $refresh_token_generate);
            switch (ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'))) {
                case "January":
                    $this->last_month = "Enero";
                    break;
                case "February":
                    $this->last_month = "Febrero";
                    break;
                case "March":
                    $this->last_month = "Marzo";
                    break;
                case "April":
                    $this->last_month = "Abril";
                    break;
                case "May":
                    $this->last_month = "Mayo";
                    break;
                case "June":
                    $this->last_month = "Junio";
                    break;
                case "July":
                    $this->last_month = "Julio";
                    break;
                case "August":
                    $this->last_month = "Agosto";
                    break;
                case "September":
                    $this->last_month = "Septiembre";
                    break;
                case "October":
                    $this->last_month = "Octubre";
                    break;
                case "November":
                    $this->last_month = "Noviembre";
                    break;
                case "December":
                    $this->last_month = "Diciembre";
                    break;
            }
        }

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
                Storage::makeDirectory('/public/state-msp-all/');
                Storage::makeDirectory('/public/downloaded_msp_information');
                Date::setLocale('es');
                // $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));

                $site24x7Url = env('SITE_24X7_API');
                $r_token = Storage::get('token/refreshToken.txt');
                // $refresh_token = $this->getRefreshToken();
                $refresh_token = $r_token;
                $state = json_decode(Storage::get('public/state-msp-all/state.json'), true);
                $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
                if ($this->msp_init != 'undefinedMSP') {
                    foreach ($customers_its as $index => $msp) {
                        if ($msp['zaaid'] != $this->msp_init) {
                            unset($customers_its[$index]);
                        } elseif ($msp['zaaid'] == $this->msp_init) {
                            break;
                        }
                    }
                }
                if ($state != null) {
                    if (array_key_exists('zaaid', $state)) {
                        foreach ($customers_its as $index => $msp) {
                            if ($msp['zaaid'] != $state['zaaid']) {
                                unset($customers_its[$index]);
                            } elseif ($msp['zaaid'] == $state['zaaid']) {
                                break;
                            }
                        }
                    }
                }

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
                        $inicio_proceso = Storage::get('inicio_procesos/InicioMasiveExport.txt');
                        if (Carbon::parse($inicio_proceso)->diffInMinutes(now()) > 55) {
                            $refresh_token = $this->getRefreshToken()->access_token;
                            Storage::put('token/refreshToken.txt', $refresh_token);
                            Storage::put('inicio_procesos/InicioMasiveExport.txt', now()->format('d-m-Y H:i:s'));
                        }
                        // dispatch(function () use ($customer_it, $site24x7Url, $state, $start_time, $customers_its) {
                        $completed_monitors = [];
                        $this->storedInformation = [];
                        $customer_id = $customer_it['user_id'];
                        $customer = $customer_it['name'];
                        $zaaid = $customer_it['zaaid'];
                        //Create File

                        $r_token = Storage::get('token/refreshToken.txt');
                        // $refresh_token = $this->getRefreshToken();
                        $refresh_token = $r_token;
                        $monitors = $this->getMonitors($site24x7Url, $zaaid, $refresh_token);
                        array_push($this->storedInformation, [
                            'id' => $customer_id,
                            'name' => $customer,
                            'zaaid' => $zaaid,
                            'monitors' => []
                        ]);
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
                            //If exists state, the process init from it
                            if ($state != null) {
                                if (array_key_exists('zaaid', $state)) {
                                    if ($zaaid == $state['zaaid']) {
                                        $this->completed_reports = $state['completed_reports'];
                                        $this->percentage = $state['percentage'];
                                        $completed_monitors_decode = json_decode($state['completed_monitors']);
                                        $monitors = array_filter($monitors, function ($monitor_item) use ($completed_monitors_decode) {
                                            return !in_array($monitor_item['monitor_id'], $completed_monitors_decode);
                                        });
                                        $completed_monitors = $completed_monitors_decode;
                                        $getDownloadInformation = json_decode(Storage::get("public/downloaded_msp_information/{$customer}_{$zaaid}.json"), true);
                                        if ($getDownloadInformation != null) {
                                            $this->storedInformation[0]['monitors'] = $getDownloadInformation[0]['monitors'];
                                        }
                                    }
                                }
                            }
                            if (count($monitors) == 0) {
                                Storage::put("public/downloaded_msp_information/{$customer}_{$zaaid}.json", "");
                                Storage::put('public/state-msp-all/state.json',  json_encode([
                                    'customer' => $customer,
                                    'zaaid' => $zaaid,
                                    'completed_monitors' => json_encode($completed_monitors),
                                    'completed_reports' => 0,
                                    'percentage' => 100,
                                    'downloaded_files' => true
                                ], JSON_PRETTY_PRINT));
                            }
                            if ($this->totalMonitors > 1000) {
                                $chunk_monitors = array_chunk($monitors, 1000, true);
                                $totalChunks = count($chunk_monitors);
                                $count_chunk = 1;
                                foreach ($chunk_monitors as $monitors) {
                                    $r_token = Storage::get('token/refreshToken.txt');
                                    // $refresh_token = $this->getRefreshToken();
                                    $refresh_token = $r_token;
                                    $this->iterateMonitors($monitors, $site24x7Url, $refresh_token, $zaaid, $customer, $start_time, $completed_monitors, $totalChunks, $count_chunk);
                                    $count_chunk++;
                                    sleep(60);
                                }
                            } else {
                                $monitorsCollect = collect();
                                $this->iterateMonitors($monitors, $site24x7Url, $refresh_token, $zaaid, $customer, $start_time, $completed_monitors);
                            }

                            $this->completed_customers++;
                            $this->percentage_customers = $this->getPercentage($this->completed_customers, count($customers_its));

                            event(new DownloadAllInformationAPI(count($customers_its), $this->percentage_customers, $this->completed_customers, $customer_id));

                            $stateJSON = json_decode(Storage::get('public/state-msp-all/state.json'));
                            $stateJSON->downloaded_files = true;
                            Storage::put('public/state-msp-all/state.json', json_encode($stateJSON, JSON_PRETTY_PRINT));
                            $texto = "[" . date("Y-m-d H:i:s") . "]: " . $customer;
                            Storage::append("tareas_programadas.txt", $texto);
                        }
                        Storage::put('public/state-msp-all/state.json', json_encode([], JSON_PRETTY_PRINT));
                        // });
                        if (Storage::get('email/email.txt')) {
                            Mail::to(Storage::get('email/email.txt'))->send(new GeneracionDeReporteFinalizada($customer));
                        }
                    }
                    // $this->generateMSPReports();
                    $texto = "[" . date("Y-m-d H:i:s") . "]: Fin de tarea de generación de informes.";
                    Storage::append("tareas_programadas.txt", $texto);
                    if (Storage::get('email/email.txt')) {
                        Mail::to(Storage::get('email/email.txt'))->send(new GeneracionDeReportesFinalizada());
                    }
                }
            }
        }
    }

    public function iterateMonitors($monitors, $site24x7Url, $refresh_token, $zaaid, $customer, $start_time, $completed_monitors, $totalChunks = null, $countChunk = null)
    {
        foreach ($monitors as $monitor) {
            $inicio_proceso = Storage::get('inicio_procesos/InicioMasiveExport.txt');
            if (Carbon::parse($inicio_proceso)->diffInMinutes(now()) > 55) {
                $refresh_token = $this->getRefreshToken()->access_token;
                Storage::put('token/refreshToken.txt', $refresh_token);
                Storage::put('inicio_procesos/InicioMasiveExport.txt', now()->format('d-m-Y H:i:s'));
            }

            $processedMonitor = $this->processSite24x7Monitors($monitor, $site24x7Url, $refresh_token, $zaaid, $customer, $this->last_month);
            $this->completed_reports++;
            $this->percentage = $this->getPercentage($this->completed_reports, $this->totalMonitors);

            // $monitorsCollect->push($processedMonitor);
            array_push($this->storedInformation[0]['monitors'], $processedMonitor);
            file_put_contents(public_path("storage/downloaded_msp_information/{$customer}_{$zaaid}.json"), json_encode($this->storedInformation));
            //SAVE STATE
            array_push($completed_monitors, $monitor['monitor_id']);
            $state_stored = json_encode([
                'customer' => $customer,
                'zaaid' => $zaaid,
                'completed_monitors' => json_encode($completed_monitors),
                'completed_reports' => $this->completed_reports,
                'percentage' => $this->percentage,
                'downloaded_files' => false
            ], JSON_PRETTY_PRINT);
            $message = "Descargando Información...";
            if ($totalChunks) {
                $message = "Fase {$countChunk} de {$totalChunks} | Descargando...";
            }
            Storage::put('public/state-msp-all/state.json', $state_stored);
            event(new DownloadInformationAPI($this->totalMonitors, $this->percentage, $this->completed_reports, $zaaid, $customer, $message));
        }
    }
    public function stopProcess()
    {
        die();
    }
}

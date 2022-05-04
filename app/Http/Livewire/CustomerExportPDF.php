<?php

namespace App\Http\Livewire;

use App\Events\ProcessReport;
use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use PHPJasper\PHPJasper;
use Jenssegers\Date\Date;

class CustomerExportPDF extends Component implements ShouldBroadcast
{
    use ApiSite24x7, ReestructurarDatosAPISite24x7, GenerarReportesSite24x7;

    public $customer;
    public $totalMonitors;
    public $zaaid;
    public $name;
    public $last_month;
    public $percentage = 0;
    public $completed_reports = 0;
    public $period_report;

    public function broadcastOn()
    {
        return new Channel('progress-report');
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
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();
        $monitors = $this->getMonitors($site24x7Url, $this->zaaid, $refresh_token);
        $this->totalMonitors = count($monitors);
        $this->completed_reports = 0;
        $this->percentage = 0;
        $this->processSite24x7Monitors($monitors, $site24x7Url, $refresh_token);
        $this->createFolderToCustomer();
        $this->percentage = 0;
    }

    public function createFolderToCustomer($monitor = null)
    {
        $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $this->last_month;
        if ($monitor) {
            $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $this->last_month . DIRECTORY_SEPARATOR . $monitor['type'];
        }
        Storage::makeDirectory('public/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports);
        return $path_reports;
    }

    public function processSite24x7Monitors($monitors, $site24x7Url, $refresh_token)
    {
        foreach ($monitors as $monitor) {
            $monitor_id = $monitor['monitor_id'];
            if ($monitor['type'] == 'SERVER' and $monitor['state'] == 0) {
                $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, "?period={$this->period_report}");
                $performance = $this->getPerformance($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
                $performance_disk = $this->getPerformance($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}&report_attribute=DISK");
                $customers = [
                    'customer' => [
                        'name' => $this->name,
                        'zaaid' => $this->zaaid,
                        'monitor' => $monitor,
                        'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                        'performance' =>  $this->applyFormatToPerformance($performance),
                        'performance_disk' => $this->applyFormatToPerformanceDisk($performance_disk),
                    ]
                ];
                $path_reports = $this->createFolderToCustomer($monitor);
                $this->getJasperReport($customers,  $this->name, $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
            } else if ($monitor['type'] == 'AGENTLESSSERVER' and $monitor['state'] == 0) {
                $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, "?period={$this->period_report}");
                $performance = $this->getPerformance($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
                $performance_disk = $this->getPerformanceCharts($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, "/AllDiskUsedChart?granularity=3&period={$this->period_report}");
                $customers = [
                    'customer' => [
                        'name' => $this->name,
                        'zaaid' => $this->zaaid,
                        'monitor' => $monitor,
                        'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                        'performance' =>  $this->applyFormatToPerformance($performance),
                        'performance_disk' => $this->applyFormatToPerformanceDiskCharts($performance_disk),
                    ]
                ];
                $path_reports = $this->createFolderToCustomer($monitor);
                $this->getJasperReport($customers,  $this->name, $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
            }
            $this->completed_reports++;
            $this->percentage = $this->getPercentage($this->completed_reports, $this->totalMonitors);
            event(new ProcessReport($this->totalMonitors, $this->percentage, $this->completed_reports, $this->zaaid));
        }
    }
}

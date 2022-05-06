<?php

namespace App\Http\Livewire;

use App\Events\ProcessReport;
use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Livewire\Component;
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

        // Se realiza el proceso de generacion de reportes
        foreach ($monitors as $monitor) {
            $this->processSite24x7Monitors($monitor, $site24x7Url, $refresh_token, $this->zaaid, $this->name, $this->last_month);
            $this->completed_reports++;
            $this->percentage = $this->getPercentage($this->completed_reports, $this->totalMonitors);
            event(new ProcessReport($this->totalMonitors, $this->percentage, $this->completed_reports, $this->zaaid, $this->name));
        }
    }
}

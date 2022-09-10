<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Jenssegers\Date\Date;

class ReportesSiteJasper extends Component
{
    public $customers_its;
    public $period = 7;
    public $showStartEndDate = false;
    public $last_month;
    public $start_custom_date;
    public $end_custom_date;
    protected $queryString = [
        'last_month' => ['except' => ''],
        'period' => ['except' => ''],
        'showStartEndDate' => ['except' => ''],
        'start_custom_date' => ['except' => ''],
        'end_custom_date' => ['except' => ''],
    ];
    public function mount($customers_its)
    {
        Date::setLocale('es');
        $this->customers_its = $customers_its;
        if (!$this->last_month) {
            $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
        }
    }

    public function updatedPeriod($value)
    {
        Date::setLocale('es');
        $this->period = $value;
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
        return view('livewire.reportes-site-jasper');
    }
}

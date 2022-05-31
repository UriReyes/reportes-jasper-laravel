<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Jenssegers\Date\Date;

class ReportesSiteJasper extends Component
{
    public $customers_its;
    public $period = 7;
    public $last_month;

    public function mount($customers_its)
    {
        Date::setLocale('es');
        $this->customers_its = $customers_its;
        $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
    }

    public function updatedPeriod($value)
    {
        Date::setLocale('es');
        if ($value == 7) {
            $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
        } else if ($value == 13) {
            $this->last_month = ucfirst(Date::now()->format('F'));
        }
    }

    public function render()
    {
        return view('livewire.reportes-site-jasper');
    }
}

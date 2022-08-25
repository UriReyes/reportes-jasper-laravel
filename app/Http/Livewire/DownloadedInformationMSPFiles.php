<?php

namespace App\Http\Livewire;

use App\Traits\GenerarReportesSite24x7;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Jenssegers\Date\Date;

class DownloadedInformationMSPFiles extends Component
{
    use GenerarReportesSite24x7;
    public $downloaded_files;
    public $last_month;

    public function mount()
    {
        Date::setLocale('es');
        $this->last_month = ucfirst(Date::now()->firstOfMonth()->subMonth()->format('F'));
        $this->downloaded_files =  Storage::disk('public')->files('downloaded_msp_information');
    }

    public function render()
    {
        return view('livewire.downloaded-information-m-s-p-files');
    }

    public function exportByDownloadedInformation(string $archivo)
    {
        $this->generateMSPReport($archivo);
    }
}

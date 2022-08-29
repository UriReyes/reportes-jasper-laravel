<?php

namespace App\Http\Controllers;

use App\Traits\ApiSite24x7;
use App\Traits\GenerarReportesSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPJasper\PHPJasper;
use Jenssegers\Date\Date;

class JasperController extends Controller
{
    use ApiSite24x7, ReestructurarDatosAPISite24x7, GenerarReportesSite24x7;
    public function index()
    {
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();

        if ($refresh_token == 'access_denied') {
            $customers_its  = [];
        } else {
            $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
            if ($customers_its == 401) {
                $customers_its = [];
            }
        }
        // dd($customers_its);
        return view('welcome', compact('customers_its'));
    }

    public function exportAll()
    {
        return view('exportAll');
    }

    public function reporteParametros($customer = null, $zaaid = null)
    {
        // CONSUMO API SITE24x7
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();
        Date::setLocale('es');
        $last_month = ucfirst(Date::now()->subMonth()->format('F'));

        $monitors = $this->getMonitors($site24x7Url, $zaaid, $refresh_token);
        foreach ($monitors as $monitor) {
            $monitor_id = $monitor['monitor_id'];
            if ($monitor['type'] == 'SERVER' and $monitor['state'] == 0) {
                $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period=7");
                $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, 'unit_of_time=3&period=7');
                $performance_disk = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, 'unit_of_time=3&period=7&report_attribute=DISK');
                $customers = [
                    'customer' => [
                        'name' => $customer,
                        'zaaid' => $zaaid,
                        'monitor' => $monitor,
                        'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                        'performance' =>  $this->applyFormatToPerformance($performance),
                        'performance_disk' => $this->applyFormatToPerformanceDisk($performance_disk),
                    ]
                ];
                $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . $customer . DIRECTORY_SEPARATOR . $last_month . DIRECTORY_SEPARATOR . $monitor['type'];
                Storage::makeDirectory('public/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports);
                $this->getJasperReport($customers,  $customer, $monitor['display_name'], $path_reports, $monitor['type']);
            }
        }

        return redirect()->route('home');
    }

    public function downloadedInformationMSP()
    {
        return view('downloaded-information');
    }

    public function folder()
    {
        return view('folder');
    }
    public function administracion()
    {
        return view('administracion');
    }

    // public function tests()
    // {
    //     dd('s');
    //     sleep(100);
    //     return response()->json(['success' => true]);
    // }
}

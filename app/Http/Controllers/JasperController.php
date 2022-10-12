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
        $refresh_token = $this->getRefreshToken()->access_token;
        if ($refresh_token == 'access_denied') {
            $customers_its  = [];
        } else {
            $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
            if ($customers_its == 401) {
                $customers_its = [];
            }
        }
        return view('welcome', compact('customers_its'));
    }

    public function exportAll()
    {
        return view('exportAll');
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
    public function cron()
    {
        return view('cron');
    }
    public function notificaciones()
    {
        return view('notificaciones');
    }
}

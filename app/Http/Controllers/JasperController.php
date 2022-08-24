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
        // $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
        // dd($customers_its);
        // foreach ($customers_its as $index => $customer_it) {
        // if ($index >= 26) {
        //     $customer = $customer_it['name'];
        //     $zaaid = $customer_it['zaaid'];
        // $customer = "BANSEFI";
        // $zaaid = "763698181";
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
        //     sleep(5);
        // }
        // }
        // return response()->json([
        //     'status' => 'ok',
        //     'msj' => '¡Reporte compilado!'
        // ]);
        return redirect()->route('home');
    }

    /* public function getJasperReport($data, $folder_name, $file_name = "Resumen", $path_reports, $type = "SERVER")
    {
        $public = public_path('jasperreports\\');
        $xmlTmpfilePath = $public . "Resumen.jrxml";
        // $textoRemplazar = "C:\\\Users\\\uriel.santiago\\\JaspersoftWorkspace\\\KIO-Jasper\\\\";
        // $contenido = file_get_contents("C:\\laragon\\www\\reportes-jasper-laravel\\public\\jasperreports\\Resumen.jrxml");
        // $templateRuta = str_replace($textoRemplazar, str_replace('\\', '\\\\', (string)$public), (string)$contenido);
        // file_put_contents($xmlTmpfilePath, $templateRuta);       

        $output_folder = storage_path('app/public') .
            '/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports . DIRECTORY_SEPARATOR . $file_name;
        $name = 'api';
        $jsonTmpfilePath = storage_path('app/public') . '/jasper/' . $name . '.json';
        $jsonTmpfile = fopen($jsonTmpfilePath, 'w');
        fwrite($jsonTmpfile, json_encode($data));
        fclose($jsonTmpfile);

        $options = [
            'format' => ['pdf'],
            'params' => [],
            'db_connection' => [
                'driver' => 'json',
                'data_file' => $jsonTmpfilePath
            ]
        ];

        $jasper = new PHPJasper;

        $output = $jasper->process(
            $xmlTmpfilePath,
            $output_folder,
            $options
        )->output();
        //Compilando el reporte graficas.jrxml
        // shell_exec('jasperstarter compile "C:/Users/uriel.santiago/JaspersoftWorkspace/KIO-Jasper/graficas.jrxml"');
        shell_exec($output);
        // dd($output);
        // $pathToFile = base_path() .
        //     '/resources/reports/pdf/Resumen.pdf';
        // return response()->file($pathToFile);
    }

    public function applyFormatToPerformance($performance)
    {
        if (array_key_exists('data', $performance)) {
            $newPerformances = [];
            foreach ($performance['data']['chart_data'] as $pfms) {
                foreach ($pfms as $pfm) {
                    if (array_key_exists('OverallCPUChart', $pfm)) {
                        $newPerformances['OverallCPUChart'] = $this->getOverallCPUChart($pfm['OverallCPUChart']);
                    }
                    if (array_key_exists('OverallMemoryChart', $pfm)) {
                        $newPerformances['OverallMemoryChart'] = $this->getOverallMemoryChart($pfm['OverallMemoryChart']);
                    }
                    if (array_key_exists('OverallDiskUtilization', $pfm)) {
                        $newPerformances['OverallDiskUtilization'] = $this->getOverallDiskUtilization($pfm['OverallDiskUtilization']);
                    }
                }
            }
            $performance['data']['chart_data'] = $newPerformances;
            return $performance;
        }
    }

    public function applyFormatToPerformanceDisk($performance)
    {

        if (array_key_exists('data', $performance)) {
            $newPerformances = [];
            foreach ($performance['data']['chart_data'] as $pfms) {
                foreach ($pfms as $pfm) {
                    if (array_key_exists('OverallDiskUsedChart', $pfm)) {
                        $newPerformances['OverallDiskUsedChart'] = $this->getOverallDiskUsedChart($pfm['OverallDiskUsedChart']);
                    }
                    if (array_key_exists('IndividualDiskUtilization', $pfm)) {
                        $newPerformances['IndividualDiskUtilization'] = $this->getIndividualDiskUtilization($pfm['IndividualDiskUtilization']);
                    }
                    if (array_key_exists('DiskIO', $pfm)) {
                        $newPerformances['DiskIO'] = $this->getDiskIO($pfm['DiskIO']);
                    }
                }
                if (array_key_exists('IndividualDiskUtilizationTimeChart', $pfms)) {
                    $newPerformances['IndividualDiskUtilizationTimeChart'] = $this->getIndividualDiskUtilizationTimeChart($pfms['IndividualDiskUtilizationTimeChart']);
                }
            }
            $performance['data']['chart_data'] = $newPerformances;
            return $performance;
        }
    }*/
}

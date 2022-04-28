<?php

namespace App\Http\Controllers;

use App\Traits\ApiSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPJasper\PHPJasper;


class JasperController extends Controller
{
    use ApiSite24x7;
    use ReestructurarDatosAPISite24x7;

    public function reporteParametros()
    {
        // CONSUMO API SITE24x7
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();
        $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
        // dd($customers_its);
        foreach ($customers_its as $customer_it) {
            $customer = $customer_it['name'];
            $zaaid = $customer_it['zaaid'];
            // $customer = "BANSEFI";
            // $zaaid = "763698181";
            $monitors = $this->getMonitors($site24x7Url, $zaaid, $refresh_token);
            foreach ($monitors as $monitor) {
                $monitor_id = $monitor['monitor_id'];
                $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token);
                $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, 'unit_of_time=3&period=7');
                $performance_disk = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, 'unit_of_time=3&period=7&report_attribute=DISK');
                if ($monitor['type'] == 'SERVER') {
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
                    $this->getJasperReport($customers,  $customer, $monitor_id . '_monitor');
                }
                sleep(2);
            }
        }
        return response()->json([
            'status' => 'ok',
            'msj' => '¡Reporte compilado!'
        ]);
    }

    public function getJasperReport($data, $folder_name, $file_name = "Resumen", $type = "SERVER")
    {
        $public = public_path('jasperreports\\');
        $xmlTmpfilePath = $public . "Resumen.jrxml";
        $textoRemplazar = "C:\\\Users\\\uriel.santiago\\\JaspersoftWorkspace\\\KIO-Jasper\\\\";
        $contenido = file_get_contents("C:\\laragon\\www\\reportes-jasper-laravel\\public\\jasperreports\\Resumen.jrxml");
        $templateRuta = str_replace($textoRemplazar, str_replace('\\', '\\\\', (string)$public), (string)$contenido);
        $templateMod = file_put_contents($xmlTmpfilePath, $templateRuta);

        Storage::makeDirectory('public/reports' . DIRECTORY_SEPARATOR . Carbon::now()->format('Y-m-d') . DIRECTORY_SEPARATOR . $folder_name);
        $output_folder = storage_path('app/public') .
            '/reports' . DIRECTORY_SEPARATOR . Carbon::now()->format('Y-m-d') . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name;
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
    }
}

<?php
//make a trait for api site 24x7
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPJasper\PHPJasper;

trait GenerarReportesSite24x7
{
    use ReestructurarDatosAPISite24x7;

    public function getJasperReport($data, $folder_name, $file_name = "Resumen", $path_reports, $monitor_id, $type = "SERVER")
    {
        $public = public_path("jasperreports/{$type}/");
        $xmlTmpfilePath = $public . "Resumen.jrxml";
        // $textoRemplazar = "C:\\\Users\\\uriel.santiago\\\JaspersoftWorkspace\\\KIO-Jasper\\\\";
        // $contenido = file_get_contents("C:\\laragon\\www\\reportes-jasper-laravel\\public\\jasperreports\\Resumen.jrxml");
        // $templateRuta = str_replace($textoRemplazar, str_replace('\\', '\\\\', (string)$public), (string)$contenido);
        // file_put_contents($xmlTmpfilePath, $templateRuta);       

        $output_folder = storage_path('app/public') .
            '/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports . DIRECTORY_SEPARATOR . $file_name;

        Storage::makeDirectory("public/jasper/{$folder_name}");
        $file_api_name = "{$folder_name}/{$monitor_id}.json";
        $jsonTmpfilePath = storage_path('app/public/jasper/') . $file_api_name;
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

    public function createFolderToCustomer($last_month, $customer_name, $monitor = null)
    {
        $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . $customer_name . DIRECTORY_SEPARATOR . $last_month;
        if ($monitor) {
            $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . $customer_name . DIRECTORY_SEPARATOR . $last_month . DIRECTORY_SEPARATOR . $monitor['type'];
        }
        Storage::makeDirectory('public/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports);
        return $path_reports;
    }

    public function processSite24x7Monitors($monitor, $site24x7Url, $refresh_token, $zaaid, $customer_name, $last_month)
    {
        $monitor_id = $monitor['monitor_id'];
        if ($monitor['type'] == 'SERVER' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_disk = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}&report_attribute=DISK");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_disk' => $this->applyFormatToPerformanceDisk($performance_disk),
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  $customer_name, $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } else if ($monitor['type'] == 'AGENTLESSSERVER' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_disk = $this->getPerformanceCharts($site24x7Url, $monitor_id, $zaaid, $refresh_token, "/AllDiskUsedChart?granularity=3&period={$this->period_report}");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_disk' => $this->applyFormatToPerformanceDiskCharts($performance_disk),
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  $customer_name, $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        }
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
                    if (array_key_exists('ResponseTimeChart', $pfm)) {
                        $newPerformances['ResponseTimeChart'] = $this->getFormattedResponse($pfm['ResponseTimeChart']);
                    }
                    if (array_key_exists('CpuUtilChart', $pfm)) {
                        $newPerformances['CpuUtilChart'] = $this->getFormattedResponse($pfm['CpuUtilChart']);
                    }
                    if (array_key_exists('CpuUtilChart', $pfm)) {
                        $newPerformances['CpuUtilChart'] = $this->getFormattedResponse($pfm['CpuUtilChart']);
                    }
                    if (array_key_exists('MemoryUtilChart', $pfm)) {
                        $newPerformances['MemoryUtilChart'] = $this->getFormattedResponse($pfm['MemoryUtilChart']);
                    }
                    if (array_key_exists('DiskUtilChart', $pfm)) {
                        $newPerformances['DiskUtilChart'] = $this->getFormattedResponse($pfm['DiskUtilChart']);
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
    public function applyFormatToPerformanceDiskCharts($performance)
    {

        if (array_key_exists('data', $performance)) {
            if (array_key_exists('AllDiskUsedChart', $performance['data'])) {
                $newPerformances = [];
                foreach ($performance['data']['AllDiskUsedChart'] as $disks) {
                    foreach ($disks as $disk) {
                        array_push($newPerformances, $this->getFormattedResponse($disk));
                    }
                    $performance['data']['AllDiskUsedChart'] = $newPerformances;
                    return $performance;
                }
            }
        }
        return [];
    }

    function getPercentage($cantidad, $total)
    {
        $porcentaje = ($cantidad * 100) / $total; // Regla de tres
        // $porcentaje = round($porcentaje, 0);  // Quitar los decimalesreturn $porcentaje;
        return round($porcentaje);
    }
}

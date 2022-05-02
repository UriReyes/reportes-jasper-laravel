<?php

namespace App\Http\Livewire;

use App\Traits\ApiSite24x7;
use App\Traits\ReestructurarDatosAPISite24x7;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use PHPJasper\PHPJasper;
use Jenssegers\Date\Date;

class CustomerExportPDF extends Component
{
    use ApiSite24x7;
    use ReestructurarDatosAPISite24x7;

    public $customer;
    public $totalMonitors;
    public $zaaid;
    public $name;
    public $last_month;
    public $percentage = 0;
    public $completed_reports = 0;
    public function mount($customer)
    {
        $this->customer = $customer;
        $this->zaaid = $customer['zaaid'];
        $this->name = $customer['name'];
        Date::setLocale('es');
        $this->last_month = ucfirst(Date::now()->subMonth()->format('F'));
    }

    public function startProcess()
    {
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();
        $monitors = $this->getMonitors($site24x7Url, $this->zaaid, $refresh_token);
        $this->totalMonitors = count($monitors);
        foreach ($monitors as $monitor) {
            $monitor_id = $monitor['monitor_id'];
            if ($monitor['type'] == 'SERVER' and $monitor['state'] == 0) {
                $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $this->zaaid, $refresh_token);
                $performance = $this->getPerformance($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, 'unit_of_time=3&period=7');
                $performance_disk = $this->getPerformance($site24x7Url, $monitor_id, $this->zaaid, $refresh_token, 'unit_of_time=3&period=7&report_attribute=DISK');
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
                $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $this->last_month . DIRECTORY_SEPARATOR . $monitor['type'];
                Storage::makeDirectory('public/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports);
                $this->getJasperReport($customers,  $this->name, $monitor['display_name'], $path_reports, $monitor['type']);
                $this->completed_reports++;
                sleep(5);
                $this->percentage = $this->getPercentage($this->completed_reports, $this->totalMonitors);
                // $this->emit('current-percentage', $current_percentage);
            }
        }
    }

    public function render()
    {
        return view('livewire.customer-export-p-d-f');
    }


    public function getJasperReport($data, $folder_name, $file_name = "Resumen", $path_reports, $type = "SERVER")
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
    }

    function getPercentage($cantidad, $total)
    {
        $porcentaje = ((float)$cantidad * 100) / $total; // Regla de tres
        // $porcentaje = round($porcentaje, 0);  // Quitar los decimalesreturn $porcentaje;
        return $porcentaje;
    }
}

<?php

namespace App\Http\Controllers;

use App\Traits\ApiSite24x7;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPJasper\PHPJasper;


class JasperController extends Controller
{
    use ApiSite24x7;

    public function compilar()
    {

        $input = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world.jrxml';

        $jasper = new PHPJasper;
        $jasper->compile($input)->execute();

        return response()->json([
            'status' => 'ok',
            'msj' => '¡Reporte compilado!'
        ]);
    }

    public function reporte()
    {
        $input = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world.jasper';
        $output = base_path() .
            '/vendor/geekcom/phpjasper/examples';
        $options = [
            'format' => ['pdf']
        ];

        $jasper = new PHPJasper;

        $output =  $jasper->process(
            $input,
            $output,
            $options
        )->output();

        shell_exec($output);

        $pathToFile = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world.pdf';
        return response()->file($pathToFile);
    }

    public function listarParametros()
    {
        $input = "C:\Users\urire\JaspersoftWorkspace\MyReports\Coffee_Landscape_5.jrxml";

        $jasper = new PHPJasper;
        $output = $jasper->listParameters($input)->execute();

        return response()->json([
            'status' => 'ok',
            'parms' => $output
        ]);
    }

    public function compilarConParametros()
    {
        # code...
        $input = "C:\Users\urire\JaspersoftWorkspace\MyReports\Coffee_Landscape_5.jrxml";

        $jasper = new PHPJasper;
        $jasper->compile($input)->execute();

        return response()->json([
            'status' => 'ok',
            'msj' => '¡Reporte compilado!'
        ]);
    }

    public function reporteParametros()
    {
        // CONSUMO API SITE24x7
        $site24x7Url = env('SITE_24X7_API');
        $refresh_token = $this->getRefreshToken();
        $customers_its = $this->getCustomers($site24x7Url, $refresh_token);
        foreach ($customers_its as $customer_it) {
            $customer = $customer_it['name'];
            $zaaid = $customer_it['zaaid'];
            $monitors = $this->getMonitors($site24x7Url, $zaaid, $refresh_token);
            foreach ($monitors as $monitor) {
                $monitor_id = $monitor['monitor_id'];
                $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token);
                $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, 'unit_of_time=3&period=7');
                if ($monitor['type'] == 'SERVER') {
                    $customers = [
                        'customer' => [
                            'name' => $customer,
                            'zaaid' => $zaaid,
                            'monitor' => $monitor,
                            'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                            'performance' =>  $this->applyFormatToPerformance($performance)
                        ]
                    ];
                    $this->getJasperReport($customers,  $customer, $monitor_id . '_monitor');

                    // dd('Creado con exito');
                }
            }
        }
        return response()->json([
            'status' => 'ok',
            'msj' => '¡Reporte compilado!'
        ]);
    }

    public function getJasperReport($data, $folder_name, $file_name = "Resumen")
    {
        $public = public_path('jasperreports\\');
        $xmlTmpfilePath = $public . "Resumen.jrxml";
        $textoRemplazar = "C:\\\Users\\\uriel.santiago\\\JaspersoftWorkspace\\\\";
        $contenido = file_get_contents("C:\\laragon\\www\\reportes-jasper-laravel\\public\\jasperreports\\Resumen.jrxml");
        $templateRuta = str_replace($textoRemplazar, str_replace('\\', '\\\\', (string)$public), (string)$contenido);
        $templateMod = file_put_contents($xmlTmpfilePath, $templateRuta);

        Storage::makeDirectory('public/reports' . DIRECTORY_SEPARATOR . Carbon::now()->format('Y-m-d') . DIRECTORY_SEPARATOR . $folder_name);
        $output_folder = storage_path('app/public') .
            '/reports' . DIRECTORY_SEPARATOR . Carbon::now()->format('Y-m-d') . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file_name . '.pdf';
        // dd($output_folder);
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
            $input,
            $output_folder,
            $options
        )->output();

        //Compilando el reporte graficas.jrxml
        // shell_exec('jasperstarter compile "C:/Users/uriel.santiago/JaspersoftWorkspace/KIO-Jasper/graficas.jrxml"');
        shell_exec($output);

        // $pathToFile = base_path() .
        //     '/resources/reports/pdf/Resumen.pdf';
        // return response()->file($pathToFile);
    }

    public function getUptimeDownTimeAndMaintenance($data)
    {
        $charts = array_map(function ($item) {
            if ($item['key'] == 'percentage_chart') {
                $item['data']['uptimes'] = array_map(function ($item) {
                    return [
                        'date' => Carbon::parse($item[0])->format('Y-m-d H:i:s'),
                        'uptime' => $item[1],
                        'downtime' => $item[2],
                        'maintenance' => $item[3],
                    ];
                }, $item['data']);
            }
            return $item;
        }, $data['data']['charts']);

        $data['data']['charts'] = $charts;

        return $data;
    }

    public function applyFormatToPerformance($performance)
    {
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

    public function getOverallCPUChart($OverallCPUChart)
    {
        $OverallCPUChart['chart_data'] = array_map(function ($item) {
            return [
                'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                'value' => array_key_exists(1, $item) ? $item[1] : null,
            ];
        },  $OverallCPUChart['chart_data']);

        return $OverallCPUChart;
    }
    public function getOverallMemoryChart($OverallMemoryChart)
    {
        $OverallMemoryChart['chart_data'] = array_map(function ($item) {
            return [
                'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                'value' => array_key_exists(1, $item) ? $item[1] : null,
            ];
        }, $OverallMemoryChart['chart_data']);

        return $OverallMemoryChart;
    }
    public function getOverallDiskUtilization($OverallDiskUtilization)
    {
        $OverallDiskUtilization['chart_data'] = array_map(function ($item) {
            return [
                'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                'performance1' => array_key_exists(1, $item) ? $item[1] : null,
                'performance2' => array_key_exists(2, $item) ? $item[2] : null,
            ];
        }, $OverallDiskUtilization['chart_data']);

        return $OverallDiskUtilization;
    }
}

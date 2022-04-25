<?php

namespace App\Http\Controllers;

use App\Traits\ApiSite24x7;
use Carbon\Carbon;
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
        // $customers = $this->getCustomers($site24x7Url);
        $customer = "SEFIA";
        $zaaid = "764241863";
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
                        'performance' =>  $performance
                    ]
                ];
                dd($performance);
                $this->getJasperReport($customers, $monitor_id . '_monitor');

                // dd('Creado con exito');
            }
        }

        // foreach ($customers as $customer) {
        //     $zaaid = $customer['zaaid'];
        //     $monitors = $this->getMonitors($site24x7Url, $zaaid);
        // }

        return response()->json([
            'status' => 'ok',
            'msj' => '¡Reporte compilado!'
        ]);
    }

    public function getJasperReport($data, $file_name = "Resumen")
    {
        $input = "C:\Users\uriel.santiago\JaspersoftWorkspace\KIO-Jasper\Resumen.jrxml";
        $output = base_path() .
            '/resources/reports/pdf/' . $file_name;
        $name = 'test';

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
            $output,
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
}

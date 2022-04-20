<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPJasper\PHPJasper;

class JasperController extends Controller
{
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

    public function getRefreshToken()
    {
        $url = env('ZOHO_API_URL');
        $response = Http::asForm()->post("{$url}/token", [
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
            'grant_type' => "refresh_token"
        ]);
        $response = $response->object();
        return $response->access_token;
    }

    public function getCustomers($url)
    {
        $authorization = "Zoho-oauthtoken {$this->getRefreshToken()}";
        $response = Http::withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization
        ])->get("{$url}/short/msp/customers");
        $response = $response->json();
        return $response['data'];
    }

    public function getMonitors($url, $zaaid)
    {
        $authorization = "Zoho-oauthtoken {$this->getRefreshToken()}";
        $cookie = "zaaid={$zaaid}";
        $response = Http::withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization,
            'Cookie' => $cookie
        ])->get("{$url}/monitors");
        $response = $response->json();
        return $response['data'];
    }

    public function getAvailabilityReport($url, $zaaid)
    {
        $authorization = "Zoho-oauthtoken {$this->getRefreshToken()}";
        $cookie = "zaaid={$zaaid}";
        $response = Http::withHeaders([
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization,
            'Cookie' => $cookie
        ])->get("{$url}/reports/availability_summary/424449000000040027?period=7");
        $response = $response->json();
        return $response;
    }

    public function reporteParametros()
    {
        // CONSUMO API SITE24x7
        $site24x7Url = env('SITE_24X7_API');
        $customers = $this->getCustomers($site24x7Url);
        $data = $this->getAvailabilityReport($site24x7Url, "764241863");
        // foreach ($customers as $customer) {
        //     $zaaid = $customer['zaaid'];
        //     $monitors = $this->getMonitors($site24x7Url, $zaaid);
        // }

        //USO DE JASPER REPORT        
        $input = "C:\Users\uriel.santiago\JaspersoftWorkspace\KIO-Jasper\Resumen.jrxml";
        $output = base_path() .
            '/resources/reports/pdf';
        $name = 'test';

        $charts = array_map(function ($item) {
            if ($item['key'] == 'percentage_chart') {
                $item['data']['uptimes'] = array_map(function ($item) {
                    return [
                        'date' => $item[0],
                        'uptime' => $item[1]
                    ];
                }, $item['data']);
            }
            return $item;
        }, $data['data']['charts']);

        $data['data']['charts'] = $charts;

        $jsonTmpfilePath = storage_path('app/public') . '/jasper/' . $name . '.json';
        $jsonTmpfile = fopen($jsonTmpfilePath, 'w');
        fwrite($jsonTmpfile, json_encode($data));
        fclose($jsonTmpfile);
        $datafile = $jsonTmpfilePath;
        $options = [
            'format' => ['pdf'],
            'params' => [],
            'db_connection' => [
                'driver' => 'json',
                'data_file' => $datafile
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

        $pathToFile = base_path() .
            '/resources/reports/pdf/Resumen.pdf';
        return response()->file($pathToFile);
    }
}

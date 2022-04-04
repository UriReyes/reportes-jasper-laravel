<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $input = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world_params.jrxml';

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
        $input = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world_params.jrxml';

        $jasper = new PHPJasper;
        $jasper->compile($input)->execute();

        return response()->json([
            'status' => 'ok',
            'msj' => '¡Reporte compilado!'
        ]);
    }
    public function reporteParametros()
    {
        $input = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world_params.jasper';
        $output = base_path() .
            '/vendor/geekcom/phpjasper/examples';
        $options = [
            'format' => ['pdf'],
            'params' => [
                'myInt' => 7,
                'myDate' => date('y-m-d'),
                'myImage' => base_path() .
                    '/vendor/geekcom/phpjasper/examples/jasperreports_logo.png',
                'myString' => 'Hola Mundo!'
            ]
        ];

        $jasper = new PHPJasper;

        $output = $jasper->process(
            $input,
            $output,
            $options
        )->output();

        shell_exec($output);

        $pathToFile = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world_params.pdf';
        return response()->file($pathToFile);
    }
}

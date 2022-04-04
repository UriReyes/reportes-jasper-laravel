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

        $jasper->process(
            $input,
            $output,
            $options
        )->execute();

        $pathToFile = base_path() .
            '/vendor/geekcom/phpjasper/examples/hello_world.pdf';
        return response()->file($pathToFile);
    }
}

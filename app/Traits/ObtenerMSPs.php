<?php
//make a trait for api site 24x7
namespace App\Traits;

use App\Models\Msp;

trait ObtenerMSPs
{
    public function generarMSPs($mspList)
    {
        $order = 0;
        foreach ($mspList as $index => $msp) {
            $mspModel = Msp::where('zaaid', $msp['zaaid']);
            if ($mspModel->exists()) {
                $order = $mspModel->first()['order'];
            } else {
                $order++;
            }
            Msp::updateOrCreate([
                'zaaid' => $msp['zaaid'],
            ], [
                'name' => $msp['name'],
                'user_id' => $msp['user_id'],
                'order' => $order,
            ]);
            //TODO: Eliminar los MSP que no se encuentren en SITE24x7
        }
        return Msp::orderBy('order')->get();
    }
}

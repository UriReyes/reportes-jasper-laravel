<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneracionDeReporteFinalizada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $msp_customer;
    public function __construct($msp_customer)
    {
        $this->msp_customer = $msp_customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.generacion-de-reporte-finalizada')->subject('Generación de Reportes del MSP: ' . $this->msp_customer . ' Finalizada');
    }
}

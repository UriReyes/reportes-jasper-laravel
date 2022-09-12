<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DownloadInformationAPI implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $total_monitors;
    public $progress;
    public $completed_reports;
    public $zaaid;
    public $customer;
    public $textType;

    public function __construct($total_monitors, $progress, $completed_reports, $zaaid, $customer = null, $textType = 'Descargando Información...')
    {
        $this->customer = $customer;
        $this->total_monitors = $total_monitors;
        $this->progress = $progress;
        $this->completed_reports = $completed_reports;
        $this->zaaid = $zaaid;
        $this->textType = $textType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return env('PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL');
    }

    public function broadcastAs()
    {
        return env('PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT');
    }
}

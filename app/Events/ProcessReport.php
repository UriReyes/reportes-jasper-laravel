<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessReport implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $total_monitors;
    public $progress;
    public $completed_reports;
    public $zaaid;
    public $customer;

    public function __construct($total_monitors, $progress, $completed_reports, $zaaid, $customer = null)
    {
        $this->customer = $customer;
        $this->total_monitors = $total_monitors;
        $this->progress = $progress;
        $this->completed_reports = $completed_reports;
        $this->zaaid = $zaaid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return 'progress-report';
    }

    public function broadcastAs()
    {
        return 'process-report';
    }
}

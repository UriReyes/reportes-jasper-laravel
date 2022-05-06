<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessReports implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $total_customers;
    public $progress;
    public $completed_customers;
    public $customer_id;

    public function __construct($total_customers, $progress, $completed_customers, $customer_id)
    {
        $this->total_customers = $total_customers;
        $this->progress = $progress;
        $this->completed_customers = $completed_customers;
        $this->customer_id = $customer_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return 'progress-reports';
    }

    public function broadcastAs()
    {
        return 'process-reports';
    }
}

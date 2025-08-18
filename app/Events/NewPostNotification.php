<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewPostNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postData;
    /**
     * Create a new event instance.
     */
    public function __construct($postData)
    {
        $this->postData = $postData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array{
        return [
            new Channel('notifications'),
        ];
    }

    public function broadcastAs(){
        return 'NewPostNotification';
    }

    public function broadcastWith(){   
        return [
            'postData' => $this->postData,
        ];
    }
}

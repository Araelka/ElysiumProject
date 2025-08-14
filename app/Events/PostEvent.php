<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $action;
    public $postData; 

    public function __construct($action, $postData){
        $this->action = $action;
        $this->postData = $postData;   
    }

    public function broadcastOn(){
        return new Channel('posts');
    }

    public function broadcastAs(){
        return 'PostEvent';
    }

    public function broadcastWith(){   
        return [
            'action' => $this->action,
            'postData' => $this->postData,
        ];
    }

}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostReadEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;


    public $postId;
    public $readerUserId; 
    public $readerCharacterName; 

    public function __construct($postId, $readerUserId, $readerCharacterName = null)
    {
        $this->postId = $postId;
        $this->readerUserId = $readerUserId;
        $this->readerCharacterName = $readerCharacterName;
    }


    public function broadcastOn(): array{
        return [
            new Channel('posts')
        ];
    }

    public function broadcastAs(){
        return 'PostRead';
    }

    public function broadcastWith(){   
        return [
            'postId' => $this->postId,
            'readerUserId' => $this->readerUserId,
            'readerCharacterName' => $this->readerCharacterName,
        ];
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class ThreadHasNewReply
{
    public $thread;
    public $reply;

    use SerializesModels;

    /**
     * Create a new event instance
     * @param \App\Thread $thread
     * @param \App\reply $reply
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadHasNewReply
{
    public $reply;

    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance
     * @param \App\Thread $thread
     * @param \App\reply $reply
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }
}

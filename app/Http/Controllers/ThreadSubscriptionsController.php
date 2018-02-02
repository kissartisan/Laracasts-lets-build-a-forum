<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadSubscriptionsController extends Controller
{
    public function store($channelID, Thread $thread)
    {
        $thread->subscribe();
    }
}

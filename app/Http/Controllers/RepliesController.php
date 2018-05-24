<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(1);
    }


    /**
     * Persist a new reply
     *
     * @param $channelId
     * @param \App\Thread $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread)
    {
        if(Gate::denies('create', new Reply))
            return response('You are posting too frequently, please take a break. :)', 422); // Unprocessable entity

        try {
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]);
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422); // Unprocessable entity
        }

        if (request()->expectsJson())
            return $reply->load(['owner']);

        return back()->with('flash', 'Your reply has been posted!');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply->update(request(['body']));
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422); // Unprocessable entity
        }

    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson())
            return response(['status' => 'Reply deleted']);

        return back();
    }
}

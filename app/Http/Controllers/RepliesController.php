<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Notifications\YouWereMentioned;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Http\Request;

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
    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        $reply = $thread->addReply([
            'body' => $form->body,
            'user_id' => auth()->id()
        ]);

        // Inspect the body of the reply for username mentions
        preg_match_all('/\@([^\s\.]+)/', $reply->body, $matches);

        $names = $matches[1];

        // Then notified each mentioned user.
        foreach ($names as $name) {
            $user = User::whereName($name)->first();

            if ($user)
                $user->notify(new YouWereMentioned($reply));
        }

        return $reply->load('owner');
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

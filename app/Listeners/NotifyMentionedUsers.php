<?php

namespace App\Listeners;

use App\Events\ThreadHasNewReply;
use App\Notifications\YouWereMentioned;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadHasNewReply  $event
     * @return void
     */
    public function handle(ThreadHasNewReply $event)
    {
        collect($event->reply->mentionedUsers())
            ->map(function($name) {
                return User::whereName($name)->first();
            })
            ->filter()
            ->each(function($user) use($event) {
                $user->notify(new YouWereMentioned($event->reply));
            });

        // // Inspect the body of the reply for username mentions
        // $mentionedUsers = $event->reply->mentionedUsers();

        // // Then notified each mentioned user.
        // foreach ($mentionedUsers as $name) {
        //     if ($user = User::whereName($name)->first())
        //         $user->notify(new YouWereMentioned($event->reply));
        // }
    }
}

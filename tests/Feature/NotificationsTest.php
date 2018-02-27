<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        // When the user subscribe to a thread
        $thread = create('App\Thread')->subscribe();

        // First, we should not a have a notification
        $this->assertCount(0, auth()->user()->notifications);

        // Even if we have a reply to the given thread
        $thread->addReply([
            'user_id' => auth()->id(),
            'body'    => 'Hello world!'
        ]);

        // We should also not have a notification
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        // But when another user reply to the authenticated user's subscribed thread
        $thread->addReply([
            'user_id' => create('App\User')->user_id,
            'body'    => 'FooBar'
        ]);

        // The authenticated user should receive a notification
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    function a_user_can_fetch_their_unread_notifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson('/profiles/' . auth()->user()->name . '/notifications')->json()
        );
    }


    /** @test */
    function a_user_can_mark_a_notification_as_read()
    {
        create(DatabaseNotification::class);

        tap(auth()->user(), function ($user) {
            // If user has unread notifications
            $this->assertCount(1, $user->unreadNotifications);

            // If we submit a delete request
            $this->delete("/profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id);

            // Then we should not see unread notification
            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }
}

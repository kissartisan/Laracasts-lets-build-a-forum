<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        // Given we have an authenticated user
        $this->signIn();

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
        // Given we have an authenticated user
        $this->signIn();

        // When the user subscribe to a thread
        $thread = create('App\Thread')->subscribe();

        // When we add a reply
        $thread->addReply([
            'user_id' => create('App\User')->user_id,
            'body'    => 'Hello world!'
        ]);

        $user = auth()->user();

        // Then we should get unread notifications
        $response = $this->getJson("/profiles/{$user->name}/notifications")->json();

        // And assert that the response count it is equal to one
        $this->assertCount(1, $response);
    }


    /** @test */
    function a_user_can_mark_a_notification_as_read()
    {
        // Given we have an authenticated user
        $this->signIn();

        // When the user subscribe to a thread
        $thread = create('App\Thread')->subscribe();

        // When we add a reply
        $thread->addReply([
            'user_id' => create('App\User')->user_id,
            'body'    => 'Hello world!'
        ]);

        $user = auth()->user();

        // If user has unread notifications
        $this->assertCount(1, $user->unreadNotifications);
        $notificationID = $user->unreadNotifications->first()->id;

        // If we submit a delete request
        $this->delete("/profiles/{$user->name}/notifications/{$notificationID}");

        // Then we should not see unread notification
        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}

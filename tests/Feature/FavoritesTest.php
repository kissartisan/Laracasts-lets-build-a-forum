<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
             ->post('replies/1/favorites')
             ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        // If I post to a "favorite" endpoint
        $this->post('replies/' . $reply->reply_id . '/favorites');

        // It should be recorded in the database
        $this->assertCount(1, $reply->favorites);
    }


    /** @test */
    function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();

        $reply = create('App\Reply');

        try {
            // If I post to a "favorite" endpoint multiple times at the same reply
            $this->post('replies/' . $reply->reply_id . '/favorites');
            $this->post('replies/' . $reply->reply_id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record set twice.');
        }

        // It should be recorded in the database
        $this->assertCount(1, $reply->favorites);
    }
}

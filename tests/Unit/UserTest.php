<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('App\User');
        $reply = create('App\Reply', ['user_id' => $user->user_id]);

        $this->assertEquals($reply->reply_id, $user->lastReply->reply_id);
    }

    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
        $user = create('App\User');
        $this->assertEquals(asset('storage/images/avatars/default.png'), asset($user->avatar_path));

        $user->avatar_path = 'images/avatars/me.jpg';
        $this->assertEquals(asset('storage/images/avatars/me.jpg'), asset($user->avatar_path));
    }

}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }


    /** @test */
    function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
             ->assertSee($this->thread->title);
    }


    /** @test */
    function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        $reply = create('App\Reply', ['thread_id' => $this->thread->thread_id]);

        // When we visit a thread page,
        // Then we should see the replies
        $response = $this->get($this->thread->path())
                        ->assertSee($reply->body);

    }
}

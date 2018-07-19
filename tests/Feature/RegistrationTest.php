<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name'                  => 'Lady Morganne',
            'email'                 => 'lml@test.com',
            'password'              => 'test1234',
            'password_confirmation' => 'test1234'
        ]);


        Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    /** @test */
    public function users_can_fully_confirm_their_email_addresses()
    {
         Mail::fake();

        $this->post(route('register'), [
            'name'                  => 'Lady Morganne',
            'email'                 => 'lml@test.com',
            'password'              => 'test1234',
            'password_confirmation' => 'test1234'
        ]);

        $user =  User::whereName('Lady Morganne')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        // Let the user confirm their account.
        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('threads'));

            tap($user->fresh(), function($user) {
                $this->assertTrue($user->confirmed);
                $this->assertNull($user->confirmation_token);
            });
    }

    /** @test */
    public function confirming_an_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'Invalid']))
             ->assertRedirect(route('threads'))
             ->assertSessionHas('flash');
    }
}

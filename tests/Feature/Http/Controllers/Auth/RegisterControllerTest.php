<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_new_user()
    {
        $this->post(route('auth.register'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'type',
                'expires_in',
            ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(1, $user->accessTokens);
    }

    /** @test */
    public function it_can_fail_to_register_a_user_with_an_existing_password()
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $this->post(route('auth.register'), [
            'email' => 'existing@example.com',
            'password' => 'Password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => 'The email has already been taken.',
            ]);
    }
}

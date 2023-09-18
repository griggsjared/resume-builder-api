<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_an_access_token_for_a_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'type',
                'expires_in',
            ]);

        $this->assertAuthenticatedAs($user);
        $this->assertCount(1, $user->accessTokens);
    }

    /** @test */
    public function it_can_fail_to_get_an_access_token_for_a_user()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post(route('auth.login'), [
            'email' => 'text@example.com',
            'password' => 'bad-password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => 'Login credentials were incorrect.',
            ]);

        $this->assertGuest();
    }
}

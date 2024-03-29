<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_authentication()
    {
        $this->post(route('auth.logout'))
            ->assertStatus(401);
    }

    /** @test */
    public function it_can_logout_with_the_current_access_token()
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken(name: 'test', expiresAt: now()->addSeconds(60))->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post(route('auth.logout'))
            ->assertOk()
            ->assertJson([
                'message' => 'Ok',
            ]);
    }

    /** @test */
    public function it_cannot_logout_if_the_current_access_token_doesnt_expire()
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post(route('auth.refresh'))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'access_token' => 'The current access token cannot be refreshed.',
            ]);
    }
}

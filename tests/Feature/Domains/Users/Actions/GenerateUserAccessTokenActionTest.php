<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Actions\GenerateUserAccessTokenAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateUserAccessTokenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_a_user_access_token(): void
    {
        $user = User::factory()->admin()->create();

        $expiresAt = now()->addDay();

        $tokenData = app(GenerateUserAccessTokenAction::class)->execute(
            UserData::from($user),
            'test-token',
            $expiresAt,
        );

        $accessToken = $user->tokens()->find($tokenData->id);

        $this->assertNotNull($accessToken);
        $this->assertIsString($tokenData->token);
        $this->assertEquals($user->id, $accessToken->tokenable_id);
        $this->assertEquals($tokenData->name, $accessToken->name);
        $this->assertEquals($tokenData->expires_at, $accessToken->expires_at);
    }
}

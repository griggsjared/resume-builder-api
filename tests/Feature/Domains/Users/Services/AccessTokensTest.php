<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use App\Domains\Users\Services\AccessTokensService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTokensTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_a_user_access_token(): void
    {
        $user = User::factory()->admin()->create();

        $expiresAt = now()->addDay();

        $tokenData = app(AccessTokensService::class)->generate(
            UserData::from($user),
            'test-token',
            $expiresAt,
        );

        $accessToken = $user->tokens()->find($tokenData->id);

        $this->assertNotNull($accessToken);
        $this->assertIsString($tokenData->access_token);
        $this->assertEquals($user->id, $accessToken->tokenable_id);
        $this->assertEquals($tokenData->name, $accessToken->name);
        $this->assertEquals($tokenData->expires_at, $accessToken->expires_at);
    }

    /** @test */
    public function it_can_refresh_a_user_access_token(): void
    {
        $accessToken = AccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create([
                'expires_at' => now()->addDay(),
            ]);

        $newExpiresAt = now()->addWeek();

        $data = app(AccessTokensService::class)->refresh(
            AccessTokenData::from($accessToken),
            $newExpiresAt,
        );

        $newAccessToken = AccessToken::find($data->id);

        $this->assertEquals($newExpiresAt->format('Y-m-d'), $newAccessToken->expires_at->format('Y-m-d'));
    }

    /** @test */
    public function it_cant_refresh_a_user_token_that_is_expired()
    {
        $accessToken = AccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create([
                'expires_at' => now()->subDay(),
            ]);

        $newExpiresAt = now()->addWeek();

        $tokenData = app(AccessTokensService::class)->refresh(
            AccessTokenData::from($accessToken),
            $newExpiresAt,
        );

        $accessToken->refresh();

        $this->assertNull($tokenData);
        $this->assertNotEquals($newExpiresAt->format('Y-m-d'), $accessToken->expires_at->format('Y-m-d'));
    }

    /** @test */
    public function it_cant_refresh_a_user_token_that_never_expires()
    {
        $accessToken = AccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create([
                'expires_at' => null,
            ]);

        $newExpiresAt = now()->addWeek();

        $tokenData = app(AccessTokensService::class)->refresh(
            AccessTokenData::from($accessToken),
            $newExpiresAt,
        );

        $accessToken->refresh();

        $this->assertNull($tokenData);
        $this->assertNull($accessToken->expires_at);
    }
}

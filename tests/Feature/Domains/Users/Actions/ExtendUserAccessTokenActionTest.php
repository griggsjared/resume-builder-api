<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Actions\ExtendUserAccessTokenAction;
use App\Domains\Users\Actions\GenerateUserAccessTokenAction;
use App\Domains\Users\Data\UserAccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtendUserAccessTokenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_extend_a_user_access_token(): void
    {
        $accessToken = UserAccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create([
                'expires_at' => now()->addDay(),
            ]);

        $newExpiresAt = now()->addWeek();

        app(ExtendUserAccessTokenAction::class)->execute(
            UserAccessTokenData::from($accessToken),
            $newExpiresAt,
        );

        $accessToken->refresh();

        $this->assertEquals($newExpiresAt->format('Y-m-d'), $accessToken->expires_at->format('Y-m-d'));
    }

    /** @test */
    public function it_cant_extend_a_user_token_that_is_expired()
    {
        $accessToken = UserAccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create([
                'expires_at' => now()->subDay(),
            ]);

        $newExpiresAt = now()->addWeek();

        $tokenData = app(ExtendUserAccessTokenAction::class)->execute(
            UserAccessTokenData::from($accessToken),
            $newExpiresAt,
        );

        $accessToken->refresh();

        $this->assertNull($tokenData);
        $this->assertNotEquals($newExpiresAt->format('Y-m-d'), $accessToken->expires_at->format('Y-m-d'));
    }

    /** @test */
    public function it_cant_extend_a_user_token_that_never_expires()
    {
        $accessToken = UserAccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create([
                'expires_at' => null,
            ]);

        $newExpiresAt = now()->addWeek();

        $tokenData = app(ExtendUserAccessTokenAction::class)->execute(
            UserAccessTokenData::from($accessToken),
            $newExpiresAt,
        );

        $accessToken->refresh();

        $this->assertNull($tokenData);
        $this->assertNull($accessToken->expires_at);
    }
}

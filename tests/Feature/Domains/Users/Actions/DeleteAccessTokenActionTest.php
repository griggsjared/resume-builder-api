<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Actions\DeleteAccessTokenAction;
use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteAccessTokenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_user_access_token(): void
    {
        $accessToken = AccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create();

        app(DeleteAccessTokenAction::class)->execute(
            AccessTokenData::from($accessToken)
        );

        $accessToken = AccessToken::find($accessToken->id);

        $this->assertNull($accessToken);
    }
}

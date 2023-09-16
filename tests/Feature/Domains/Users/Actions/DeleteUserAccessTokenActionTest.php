<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Actions\DeleteUserAccessTokenAction;
use App\Domains\Users\Data\UserAccessTokenData;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserAccessTokenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_user_access_token(): void
    {
        $accessToken = UserAccessToken::factory()
            ->for(User::factory()->admin(), 'tokenable')
            ->create();

        app(DeleteUserAccessTokenAction::class)->execute(
            UserAccessTokenData::from($accessToken)
        );

        $accessToken = UserAccessToken::find($accessToken->id);

        $this->assertNull($accessToken);
    }
}

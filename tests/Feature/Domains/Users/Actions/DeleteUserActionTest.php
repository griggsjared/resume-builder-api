<?php

namespace Tests\Feature\Domains\Users\Actions;

use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_user(): void
    {
        $user = User::factory()->create();

        $data = app(DeleteUserAction::class)->execute(
            UserData::from($user)
        );

        $user = User::find($data->id);

        $this->assertNull($user);
    }
}

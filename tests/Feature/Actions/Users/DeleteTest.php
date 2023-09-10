<?php

namespace Tests\Feature\Actions\Users;

use App\Actions\Users\Delete;
use App\Models\DTO\UserData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function is_user_deleted()
    {
        $user = User::factory()->create();

        app(Delete::class)->handle(UserData::from($user));

        $user = User::find($user->id);

        $this->assertNull($user);
    }
}

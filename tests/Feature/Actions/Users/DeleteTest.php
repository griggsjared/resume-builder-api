<?php

namespace Tests\Feature\Actions\Users;

use App\Actions\Users\Delete;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_is_deleted()
    {
        $user = User::factory()->create();

        $deleted = (new Delete)->execute($user);

        $user = User::whereUuid($user->uuid)->first();

        $this->assertTrue($deleted);
        $this->assertNull($user);
    }
}

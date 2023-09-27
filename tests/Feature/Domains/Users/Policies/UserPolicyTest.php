<?php

namespace Tests\Feature\Domains\Users\Policies;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_allow_a_user_to_view_any_users()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertTrue($basic->can('viewAny', User::class)); // can view but will only see themselves
    }

    /** @test */
    public function it_can_allow_a_user_to_view_a_user()
    {
        $admin = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $this->assertTrue($admin->can('view', $admin));
        $this->assertTrue($admin->can('view', $admin2));
        $this->assertTrue($basic->can('view', $basic));
        $this->assertFalse($basic->can('view', $basic2));
    }

    /** @test */
    public function it_can_allow_a_user_to_create_a_user()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($admin->can('create', User::class));
        $this->assertFalse($basic->can('create', User::class));
    }

    /** @test */
    public function it_can_allow_a_user_to_update_a_user()
    {
        $admin = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $this->assertTrue($admin->can('update', $admin));
        $this->assertTrue($admin->can('update', $admin2));
        $this->assertTrue($admin->can('update', $basic));

        $this->assertFalse($basic->can('update', $admin));
        $this->assertTrue($basic->can('update', $basic));
        $this->assertFalse($basic->can('update', $basic2));
    }

    /** @test */
    public function it_can_allow_a_user_to_delete_a_user()
    {
        $admin = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $this->assertFalse($admin->can('delete', $admin));
        $this->assertTrue($admin->can('delete', $admin2));
        $this->assertTrue($admin->can('delete', $basic));

        $this->assertFalse($basic->can('delete', $admin));
        $this->assertFalse($basic->can('delete', $basic));
        $this->assertFalse($basic->can('delete', $basic2));
    }

    /** @test */
    public function it_can_allow_a_user_to_change_a_users_role()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($admin->can('changeRole', $basic));
        $this->assertFalse($basic->can('changeRole', $admin));
    }
}

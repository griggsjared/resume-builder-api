<?php

namespace Tests\Feature\Domains\Users\Policies;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_decide_if_user_can_view_any_users()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('viewAny', User::class));
        $this->assertFalse($basic->can('viewAny', User::class));
    }

    /** @test */
    public function it_can_decide_if_user_can_view_a_user()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $superAdmin2 = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('view', $superAdmin));
        $this->assertTrue($superAdmin->can('view', $superAdmin2));
        $this->assertFalse($basic->can('view', $basic));
        $this->assertFalse($basic->can('view', $basic2));
    }

    /** @test */
    public function it_can_decide_if_user_can_create_a_user()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('create', User::class));
        $this->assertFalse($basic->can('create', User::class));
    }

    /** @test */
    public function it_can_decide_if_user_can_update_a_user()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $superAdmin2 = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $this->assertFalse($superAdmin->can('update', $superAdmin));
        $this->assertTrue($superAdmin->can('update', $superAdmin2));
        $this->assertTrue($superAdmin->can('update', $basic));

        $this->assertFalse($basic->can('update', $superAdmin));
        $this->assertFalse($basic->can('update', $basic));
        $this->assertFalse($basic->can('update', $basic2));
    }

    /** @test */
    public function it_can_decide_if_user_can_delete_a_user()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $superAdmin2 = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $this->assertFalse($superAdmin->can('delete', $superAdmin));
        $this->assertTrue($superAdmin->can('delete', $superAdmin2));
        $this->assertTrue($superAdmin->can('delete', $basic));

        $this->assertFalse($basic->can('delete', $superAdmin));
        $this->assertFalse($basic->can('delete', $basic));
        $this->assertFalse($basic->can('delete', $basic2));
    }
}

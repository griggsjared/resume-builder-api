<?php

namespace Tests\Feature\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_roles_can_view_all_subjects()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('viewAny', Subject::class));
        $this->assertTrue($basic->can('viewAny', Subject::class));
    }

    /** @test */
    public function user_roles_can_view_subjects()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'author')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'author')
            ->create();

        $this->assertTrue($superAdmin->can('view', $subject));
        $this->assertTrue($superAdmin->can('view', $subject2));
        $this->assertTrue($basic->can('view', $subject));
        $this->assertFalse($basic->can('view', $subject2));
        $this->assertFalse($basic2->can('view', $subject));
        $this->assertTrue($basic2->can('view', $subject2));
    }

    /** @test */
    public function user_roles_can_create_subjects()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('create', Subject::class));
        $this->assertTrue($basic->can('create', Subject::class));
    }

    /** @test */
    public function user_roles_can_update_subjects()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'author')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'author')
            ->create();

        $this->assertTrue($superAdmin->can('update', $subject));
        $this->assertTrue($superAdmin->can('update', $subject2));
        $this->assertTrue($basic->can('update', $subject));
        $this->assertFalse($basic->can('update', $subject2));
        $this->assertFalse($basic2->can('update', $subject));
        $this->assertTrue($basic2->can('update', $subject2));
    }

    /** @test */
    public function user_roles_can_delete_subjects()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'author')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'author')
            ->create();

        $this->assertTrue($superAdmin->can('delete', $subject));
        $this->assertTrue($superAdmin->can('delete', $subject2));
        $this->assertTrue($basic->can('delete', $subject));
        $this->assertFalse($basic->can('delete', $subject2));
        $this->assertFalse($basic2->can('delete', $subject));
        $this->assertTrue($basic2->can('delete', $subject2));
    }
}

<?php

namespace Tests\Feature\Domains\Resumes\Policies;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_allow_a_user_to_view_any_subjects()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($admin->can('viewAny', Subject::class));
        $this->assertTrue($basic->can('viewAny', Subject::class));
    }

    /** @test */
    public function it_can_allow_a_user_to_view_a_subject()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'user')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'user')
            ->create();

        $this->assertTrue($admin->can('view', $subject));
        $this->assertTrue($admin->can('view', $subject2));
        $this->assertTrue($basic->can('view', $subject));
        $this->assertFalse($basic->can('view', $subject2));
        $this->assertFalse($basic2->can('view', $subject));
        $this->assertTrue($basic2->can('view', $subject2));
    }

    /** @test */
    public function it_can_allow_a_user_to_create_a_subject()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($admin->can('create', Subject::class));
        $this->assertTrue($basic->can('create', Subject::class));
    }

    /** @test */
    public function it_can_allow_a_user_to_update_a_subject()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'user')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'user')
            ->create();

        $this->assertTrue($admin->can('update', $subject));
        $this->assertTrue($admin->can('update', $subject2));
        $this->assertTrue($basic->can('update', $subject));
        $this->assertFalse($basic->can('update', $subject2));
        $this->assertFalse($basic2->can('update', $subject));
        $this->assertTrue($basic2->can('update', $subject2));
    }

    /** @test */
    public function it_can_allow_a_user_to_delete_a_subject()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'user')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'user')
            ->create();

        $this->assertTrue($admin->can('delete', $subject));
        $this->assertTrue($admin->can('delete', $subject2));
        $this->assertTrue($basic->can('delete', $subject));
        $this->assertFalse($basic->can('delete', $subject2));
        $this->assertFalse($basic2->can('delete', $subject));
        $this->assertTrue($basic2->can('delete', $subject2));
    }

    /** @test */
    public function it_can_allow_a_user_to_assign_a_user_to_a_subject()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($admin->can('assignUser', Subject::class));
        $this->assertFalse($basic->can('assignUser', Subject::class));
    }
}

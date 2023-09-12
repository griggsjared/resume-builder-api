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
    public function it_can_decide_if_user_can_view_any_subjects()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('viewAny', Subject::class));
        $this->assertFalse($basic->can('viewAny', Subject::class));
    }

    /** @test */
    public function it_can_decide_if_user_can_view_a_subject()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'user')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'user')
            ->create();

        $this->assertTrue($superAdmin->can('view', $subject));
        $this->assertTrue($superAdmin->can('view', $subject2));
        $this->assertTrue($basic->can('view', $subject));
        $this->assertFalse($basic->can('view', $subject2));
        $this->assertFalse($basic2->can('view', $subject));
        $this->assertTrue($basic2->can('view', $subject2));
    }

    /** @test */
    public function it_can_decide_if_user_can_create_a_subject()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();

        $this->assertTrue($superAdmin->can('create', Subject::class));
        $this->assertTrue($basic->can('create', Subject::class));
    }

    /** @test */
    public function it_can_decide_if_user_can_update_a_subject()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'user')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'user')
            ->create();

        $this->assertTrue($superAdmin->can('update', $subject));
        $this->assertTrue($superAdmin->can('update', $subject2));
        $this->assertTrue($basic->can('update', $subject));
        $this->assertFalse($basic->can('update', $subject2));
        $this->assertFalse($basic2->can('update', $subject));
        $this->assertTrue($basic2->can('update', $subject2));
    }

    /** @test */
    public function it_can_decide_if_user_can_delete_a_subject()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $basic = User::factory()->basic()->create();
        $basic2 = User::factory()->basic()->create();

        $subject = Subject::factory()
            ->for($basic, 'user')
            ->create();

        $subject2 = Subject::factory()
            ->for($basic2, 'user')
            ->create();

        $this->assertTrue($superAdmin->can('delete', $subject));
        $this->assertTrue($superAdmin->can('delete', $subject2));
        $this->assertTrue($basic->can('delete', $subject));
        $this->assertFalse($basic->can('delete', $subject2));
        $this->assertFalse($basic2->can('delete', $subject));
        $this->assertTrue($basic2->can('delete', $subject2));
    }
}

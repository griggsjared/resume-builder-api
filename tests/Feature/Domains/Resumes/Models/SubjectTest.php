<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('subjects', 'id'));
        $this->assertTrue(Schema::hasColumn('subjects', 'user_id'));
        $this->assertTrue(Schema::hasColumn('subjects', 'first_name'));
        $this->assertTrue(Schema::hasColumn('subjects', 'last_name'));
        $this->assertTrue(Schema::hasColumn('subjects', 'email'));
        $this->assertTrue(Schema::hasColumn('subjects', 'phone_number'));
        $this->assertTrue(Schema::hasColumn('subjects', 'city'));
        $this->assertTrue(Schema::hasColumn('subjects', 'state'));
        $this->assertTrue(Schema::hasColumn('subjects', 'overview'));
        $this->assertTrue(Schema::hasColumn('subjects', 'created_at'));
        $this->assertTrue(Schema::hasColumn('subjects', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $subject = Subject::factory()
            ->for(User::factory(), 'user')
            ->has(SubjectHighlight::factory(6), 'highlights')
            ->has(Employer::factory(3), 'employers')
            ->has(Skill::factory(3), 'skills')
            ->has(Education::factory(3), 'education')
            ->create();

        $this->assertInstanceOf(User::class, $subject->user);
        $this->assertCount(6, $subject->highlights);
        $this->assertCount(3, $subject->employers);
        $this->assertCount(3, $subject->skills);
        $this->assertCount(3, $subject->education);
    }

    /** @test */
    public function it_can_assess_a_full_name_attribute()
    {
        $user = Subject::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals($user->full_name, $user->first_name.' '.$user->last_name);
    }

    /** @test */
    public function it_can_scope_to_authorized_subjects()
    {
        $admin = User::factory()->admin()->create();
        $basic = User::factory()->basic()
            ->has(Subject::factory()->count(2))
            ->create();

        Subject::factory()->create();

        $this->assertCount(3, Subject::authorized($admin)->get());
        $this->assertCount(2, Subject::authorized($basic)->get());
    }

    /** @test */
    public function it_can_scope_to_a_search_term()
    {
        Subject::factory()->create([
            'first_name' => 'found first name',
            'last_name' => 'found last name',
            'title' => 'found title',
            'email' => 'found email',
            'phone_number' => 'found phone number',
            'city' => 'found city',
            'state' => 'found state',
            'overview' => 'found overview',
        ]);

        Subject::factory()->create([
            'first_name' => 'missing first name',
            'last_name' => 'missing last name',
            'title' => 'missing title',
            'email' => 'missing email',
            'phone_number' => 'missing phone number',
            'city' => 'missing city',
            'state' => 'missing state',
            'overview' => 'missing overview',
        ]);

        $this->assertCount(1, Subject::search('found')->get());
        $this->assertCount(2, Subject::search('first name')->get());
        $this->assertCount(2, Subject::search('last name')->get());
        $this->assertCount(2, Subject::search('title')->get());
        $this->assertCount(2, Subject::search('email')->get());
        $this->assertCount(2, Subject::search('city')->get());
        $this->assertCount(2, Subject::search('state')->get());
        $this->assertCount(2, Subject::search('overview')->get());
        $this->assertCount(0, Subject::search('zero')->get());
    }
}

<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Resumes\Models\SubjectImage;
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
        $this->assertTrue(Schema::hasColumn('subjects', 'author_id'));
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
            ->for(User::factory(), 'author')
            ->has(SubjectHighlight::factory(6), 'highlights')
            ->has(Employer::factory(3), 'employers')
            ->has(Skill::factory(3), 'skills')
            ->create();

        $this->assertCount(6, $subject->highlights);
        $this->assertCount(3, $subject->employers);
        $this->assertCount(3, $subject->skills);
        $this->assertInstanceOf(User::class, $subject->author);
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
}

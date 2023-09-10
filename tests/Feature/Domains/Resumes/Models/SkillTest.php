<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('skills', 'id'));
        $this->assertTrue(Schema::hasColumn('skills', 'subject_id'));
        $this->assertTrue(Schema::hasColumn('skills', 'name'));
        $this->assertTrue(Schema::hasColumn('skills', 'category'));
        $this->assertTrue(Schema::hasColumn('skills', 'sort'));
        $this->assertTrue(Schema::hasColumn('skills', 'created_at'));
        $this->assertTrue(Schema::hasColumn('skills', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $skill = Skill::factory()
            ->for(Subject::factory(), 'subject')
            ->create();

        $this->assertInstanceOf(Skill::class, $skill);
        $this->assertInstanceOf(Subject::class, $skill->subject);
    }
}

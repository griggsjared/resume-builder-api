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

    /** @test */
    public function it_can_scope_to_a_search_term()
    {
        Skill::factory()->create([
            'name' => 'found name',
            'category' => 'found category',
        ]);


        Skill::factory()->create([
            'name' => 'missing name',
            'category' => 'missing category',
        ]);


        $this->assertCount(1, Skill::search('found')->get());
        $this->assertCount(2, Skill::search('name')->get());
        $this->assertCount(2, Skill::search('category')->get());
        $this->assertCount(0, Skill::search('zero')->get());
    }
}

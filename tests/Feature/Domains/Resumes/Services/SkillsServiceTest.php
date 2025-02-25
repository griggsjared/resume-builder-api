<?php

namespace Tests\Feature\Domains\Resumes\Services;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\SkillsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_skill(): void
    {
        $subject = Subject::factory()->create();

        $data = app(SkillsService::class)->upsert(
            SkillData::from([
                'name' => 'PHP',
                'category' => 'Programming Language',
                'sort' => 1,
                'subject' => $subject,
            ])
        );

        $skill = Skill::find($data->id);

        $this->assertInstanceOf(Subject::class, $skill->subject);
        $this->assertEquals($subject->id, $skill->subject->id);
        $this->assertEquals($data->name, $skill->name);
        $this->assertEquals($data->category, $skill->category);
        $this->assertEquals($data->sort, $skill->sort);
    }

    /** @test */
    public function it_can_update_a_skill()
    {
        $skill = Skill::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $data = app(SkillsService::class)->upsert(
            SkillData::from([
                ...$skill->toArray(),
                'name' => 'PHP',
                'category' => 'Programming Language',
                'sort' => 1,
            ])
        );

        $skill->refresh();

        $this->assertEquals($data->name, $skill->name);
        $this->assertEquals($data->category, $skill->category);
        $this->assertEquals($data->sort, $skill->sort);
    }

    /** @test */
    public function it_can_update_a_skill_with_a_subject()
    {
        $skill = Skill::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $subject = Subject::factory()->create();

        app(SkillsService::class)->upsert(
            SkillData::from([
                ...$skill->toArray(),
                'subject' => $subject,
            ])
        );

        $skill->refresh();

        $this->assertInstanceOf(Subject::class, $skill->subject);
        $this->assertEquals($subject->id, $skill->subject->id);
    }

    /** @test */
    public function it_can_delete_a_skill(): void
    {
        $skill = Skill::factory()->create();

        app(SkillsService::class)->delete(SkillData::from($skill));

        $skill = Skill::find($skill->id);

        $this->assertNull($skill);
    }
}

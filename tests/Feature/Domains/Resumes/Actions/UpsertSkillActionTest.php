<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\UpsertSkillAction;
use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertSkillActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_skill(): void
    {
        $subject = Subject::factory()->create();

        $data = app(UpsertSkillAction::class)->execute(
            SkillData::from([
                'name' => 'PHP',
                'category' => 'Programming Language',
                'sort'  => 1,
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

        $data = app(UpsertSkillAction::class)->execute(
            SkillData::from([
                ...$skill->toArray(),
                'name' => 'PHP',
                'category' => 'Programming Language',
                'sort'  => 1,
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

        $data = app(UpsertSkillAction::class)->execute(
            SkillData::from([
                ...$skill->toArray(),
                'subject' => $subject,
            ])
        );

        $skill->refresh();

        $this->assertInstanceOf(Subject::class, $skill->subject);
        $this->assertEquals($subject->id, $skill->subject->id);
    }
}

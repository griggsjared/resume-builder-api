<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteSkillAction;
use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteSkillActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_skill(): void
    {
        $skill = Skill::factory()->create();

        app(DeleteSkillAction::class)->execute(SkillData::from($skill));

        $skill = Skill::find($skill->id);

        $this->assertNull($skill);
    }
}

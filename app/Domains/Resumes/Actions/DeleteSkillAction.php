<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;

class DeleteSkillAction
{
    public function handle(SkillData $skillData): ?SkillData
    {
        $skill = Skill::find($skillData->id);

        if (! $skill) {
            return null;
        }

        $skill->delete();

        return SkillData::from($skill);
    }
}

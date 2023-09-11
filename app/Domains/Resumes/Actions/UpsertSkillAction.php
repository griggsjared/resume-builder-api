<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;

class UpsertSkillAction
{
    public function execute(SkillData $data): SkillData
    {
        $skill = Skill::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'category' => $data->category,
                'sort' => $data->sort ?? 9999,
            ]
        );

        if ($data->subject instanceof SubjectData) {
            $skill->subject()->associate($data->subject->id);
        }

        return SkillData::from($skill);
    }
}

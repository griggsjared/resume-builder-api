<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;

class UpsertSkillAction
{
    public function handle(SkillData $data): SkillData
    {
        $skill = Skill::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'category' => $data->category,
                'sort' => $data->sort ?? 9999,
            ]
        );

        if ($data->subject) {
            $skill->subject()->associate($data->subject->id);
        } else {
            $skill->subject()->dissociate();
        }

        return SkillData::from($skill);
    }
}

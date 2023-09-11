<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;

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

        if ($data->subject instanceof SubjectData && $subject = Subject::find($data->subject?->id)) {
            $skill->subject()->associate($subject);
        }

        $skill->save();

        return SkillData::from($skill);
    }
}

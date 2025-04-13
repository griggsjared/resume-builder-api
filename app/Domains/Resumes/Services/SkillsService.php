<?php

declare(strict_types=1);

namespace App\Domains\Resumes\Services;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;

class SkillsService
{
    public function upsert(SkillData $data): SkillData
    {
        $skill = Skill::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'category' => $data->category,
                'sort' => $data->sort ?? 9999,
            ]
        );

        if ($data->subject instanceof SubjectData && $subject = Subject::find($data->subject->id)) {
            $skill->subject()->associate($subject);
        }

        $skill->save();

        return SkillData::from($skill);
    }

    public function delete(SkillData $skillData): ?SkillData
    {
        $skill = Skill::find($skillData->id);

        if (! $skill) {
            return null;
        }

        $skill->delete();

        return SkillData::from($skill);
    }
}

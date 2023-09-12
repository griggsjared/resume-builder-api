<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;

class DeleteSubjectAction
{
    public function __construct(
        private DeleteSubjectHighlightAction $deleteSubjectHighlightAction,
        private DeleteSkillAction $deleteSkillAction,
        private DeleteEmployerAction $deleteEmployerAction,
    ) {
    }

    public function execute(SubjectData $subject): ?SubjectData
    {
        $subject = Subject::find($subject->id);

        if (! $subject) {
            return null;
        }

        $subject->highlights->each(function ($highlight) {
            $this->deleteSubjectHighlightAction->execute(
                SubjectHighlightData::from($highlight)
            );
        });

        $subject->skills->each(function ($skill) {
            $this->deleteSkillAction->execute(
                SkillData::from($skill)
            );
        });

        $subject->employers->each(function ($employer) {
            $this->deleteEmployerAction->execute(
                EmployerData::from($employer)
            );
        });

        $subject->delete();

        return SubjectData::from($subject);
    }
}

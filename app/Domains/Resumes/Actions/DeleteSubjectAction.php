<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Subject;

class DeleteSubjectAction
{
    public function handle(SubjectData $subject): ?SubjectData
    {
        $subject = Subject::find($subject->id);

        if (! $subject) {
            return null;
        }

        $subject->delete();

        return SubjectData::from($subject);
    }
}

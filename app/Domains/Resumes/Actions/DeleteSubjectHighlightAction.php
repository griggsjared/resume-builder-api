<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\SubjectHighlight;

class DeleteSubjectHighlightAction
{
    public function execute(SubjectHighlightData $subjectHighlightData): ?SubjectHighlightData
    {
        $subjectHighlight = SubjectHighlight::find($subjectHighlightData->id);

        if (! $subjectHighlight) {
            return null;
        }

        $subjectHighlight->delete();

        return SubjectHighlightData::from($subjectHighlight);
    }
}

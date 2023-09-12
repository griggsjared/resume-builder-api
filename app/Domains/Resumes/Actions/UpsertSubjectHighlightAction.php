<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;

class UpsertSubjectHighlightAction
{
    public function execute(SubjectHighlightData $data): SubjectHighlightData
    {
        $subjectHighlight = SubjectHighlight::updateOrCreate(
            ['id' => $data->id],
            [
                'content' => $data->content,
            ]
        );

        if ($data->subject instanceof SubjectData && $subject = Subject::find($data->subject?->id)) {
            $subjectHighlight->subject()->associate($subject);
        }

        $subjectHighlight->save();

        return SubjectHighlightData::from($subjectHighlight);
    }
}

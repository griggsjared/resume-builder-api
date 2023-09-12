<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\EducationHighlight;

class DeleteEducationHighlightAction
{
    public function execute(EducationHighlightData $educationHighlightData): ?EducationHighlightData
    {
        $educationHighlight = EducationHighlight::find($educationHighlightData->id);

        if (! $educationHighlight) {
            return null;
        }

        $educationHighlight->delete();

        return EducationHighlightData::from($educationHighlight);
    }
}

<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\Education;

class DeleteEducationAction
{
    public function __construct(
        private DeleteEducationHighlightAction $deleteEducationHighlightAction
    ) {
    }

    public function execute(EducationData $educationData): ?EducationData
    {
        $education = Education::find($educationData->id);

        if (! $education) {
            return null;
        }

        $education->highlights->each(function ($highlight) {
            $this->deleteEducationHighlightAction->execute(
                EducationHighlightData::from($highlight)
            );
        });

        $education->delete();

        return EducationData::from($education);
    }
}

<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;

class UpsertEducationHighlightAction
{
    public function execute(EducationHighlightData $data): EducationHighlightData
    {
        $educationHighlight = EducationHighlight::updateOrCreate(
            ['id' => $data->id],
            [
                'content' => $data->content,
            ]
        );

        if ($data->education instanceof EducationData && $education = Education::find($data->education?->id)) {
            $educationHighlight->education()->associate($education);
        }

        $educationHighlight->save();

        return EducationHighlightData::from($educationHighlight);
    }
}

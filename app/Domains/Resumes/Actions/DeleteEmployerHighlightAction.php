<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\EmployerHighlight;

class DeleteEmployerHighlightAction
{
    public function execute(EmployerHighlightData $employerHighlightData): ?EmployerHighlightData
    {
        $employerHighlight = EmployerHighlight::find($employerHighlightData->id);

        if (! $employerHighlight) {
            return null;
        }

        $employerHighlight->delete();

        return EmployerHighlightData::from($employerHighlight);
    }
}

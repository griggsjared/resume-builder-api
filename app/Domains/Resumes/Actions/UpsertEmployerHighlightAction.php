<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;

class UpsertEmployerHighlightAction
{
    public function execute(EmployerHighlightData $data): EmployerHighlightData
    {
        $employerHighlight = EmployerHighlight::updateOrCreate(
            ['id' => $data->id],
            [
                'content' => $data->content,
                'sort' => $data->sort ?? 9999,
            ]
        );

        if ($data->employer instanceof EmployerData && $employer = Employer::find($data->employer?->id)) {
            $employerHighlight->employer()->associate($employer);
        }

        $employerHighlight->save();

        return EmployerHighlightData::from($employerHighlight);
    }
}

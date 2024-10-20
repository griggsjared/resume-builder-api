<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\Employer;

class DeleteEmployerAction
{
    public function __construct(
        private DeleteEmployerHighlightAction $deleteEmployerHighlightAction
    ) {}

    public function execute(EmployerData $employerData): ?EmployerData
    {
        $employer = Employer::find($employerData->id);

        if (! $employer) {
            return null;
        }

        $employer->highlights->each(function ($highlight) {
            $this->deleteEmployerHighlightAction->execute(
                EmployerHighlightData::from($highlight)
            );
        });

        $employer->delete();

        return EmployerData::from($employer);
    }
}

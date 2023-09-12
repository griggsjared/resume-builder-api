<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Subject;
use Spatie\LaravelData\DataCollection;

class UpsertEmployerAction
{
    public function __construct(
        private UpsertEmployerHighlightAction $upsertEmployerHighlightAction,
        private DeleteEmployerHighlightAction $deleteEmployerHighlightAction,
    ) {
    }

    public function execute(EmployerData $data): EmployerData
    {
        $employer = Employer::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'city' => $data->city,
                'state' => $data->state,
            ]
        );

        $employer->started_at = $data->started_at;
        $employer->ended_at = $data->ended_at;

        if ($data->subject instanceof SubjectData && $subject = Subject::find($data->subject?->id)) {
            $employer->subject()->associate($subject);
        }

        if ($data->highlights instanceof DataCollection) {

            $employer->highlights->filter(
                fn ($highlight) => ! $data->highlights->toCollection()->contains('id', $highlight->id)
            )->each(function ($highlight) {
                $this->deleteEmployerHighlightAction->execute(
                    EmployerHighlightData::from($highlight)
                );
            });

            $data->highlights->each(function ($highlight) use ($employer) {
                $this->upsertEmployerHighlightAction->execute(
                    EmployerHighlightData::from([
                        ...$highlight->toArray(),
                        'employer' => $employer,
                    ])
                );
            });
        }

        $employer->save();

        return EmployerData::from($employer);
    }
}

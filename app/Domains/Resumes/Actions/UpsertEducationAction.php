<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\Subject;
use Spatie\LaravelData\DataCollection;

class UpsertEducationAction
{
    public function __construct(
        private UpsertEducationHighlightAction $upsertEducationHighlightAction,
        private DeleteEducationHighlightAction $deleteEducationHighlightAction,
    ) {
    }

    public function execute(EducationData $data): EducationData
    {
        $education = Education::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'city' => $data->city,
                'state' => $data->state,
                'major_degree' => $data->major_degree,
                'minor_degree' => $data->minor_degree,
            ]
        );

        $education->started_at = $data->started_at;
        $education->ended_at = $data->ended_at;
        $education->earned_major_degree = $data->earned_major_degree ?? false;
        $education->earned_minor_degree = $data->earned_minor_degree ?? false;

        if ($data->subject instanceof SubjectData && $subject = Subject::find($data->subject?->id)) {
            $education->subject()->associate($subject);
        }

        if ($data->highlights instanceof DataCollection) {

            $education->highlights->filter(
                fn ($highlight) => ! $data->highlights->toCollection()->contains('id', $highlight->id)
            )->each(function ($highlight) {
                $this->deleteEducationHighlightAction->execute(
                    EducationHighlightData::from($highlight)
                );
            });

            $data->highlights->each(function ($highlight) use ($education) {
                $this->upsertEducationHighlightAction->execute(
                    EducationHighlightData::from([
                        ...$highlight->toArray(),
                        'education' => $education,
                    ])
                );
            });
        }

        $education->save();

        return EducationData::from($education);
    }
}

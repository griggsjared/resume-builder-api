<?php

declare(strict_types=1);

namespace App\Domains\Resumes\Services;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Support\Collection;

class EmployersService
{
    public function upsert(EmployerData $data): EmployerData
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

        if ($data->highlights instanceof Collection) {

            $employer->highlights->filter(
                fn ($highlight) => ! $data->highlights->contains('id', $highlight->id)
            )->each(function ($highlight) {
                $this->deleteHighlight(
                    EmployerHighlightData::from($highlight)
                );
            });

            $data->highlights->each(function ($highlight) use ($employer) {
                $this->upsertHighlight(
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

    public function delete(EmployerData $employerData): ?EmployerData
    {
        $employer = Employer::find($employerData->id);

        if (! $employer) {
            return null;
        }

        $employer->highlights->each(function ($highlight) {
            $this->deleteHighlight(
                EmployerHighlightData::from($highlight)
            );
        });

        $employer->delete();

        return EmployerData::from($employer);
    }

    public function upsertHighlight(EmployerHighlightData $data): EmployerHighlightData
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

    public function deleteHighlight(EmployerHighlightData $employerHighlightData): ?EmployerHighlightData
    {
        $employerHighlight = EmployerHighlight::find($employerHighlightData->id);

        if (! $employerHighlight) {
            return null;
        }

        $employerHighlight->delete();

        return EmployerHighlightData::from($employerHighlight);
    }
}

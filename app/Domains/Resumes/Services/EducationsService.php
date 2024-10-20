<?php

declare(strict_types=1);

namespace App\Domains\Resumes\Services;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Support\Collection;

class EducationsService
{
    public function upsert(EducationData $data): EducationData
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

        if ($data->highlights instanceof Collection) {

            $education->highlights->filter(
                fn ($highlight) => ! $data->highlights->contains('id', $highlight->id)
            )->each(function ($highlight) {
                $this->deleteHighlight(
                    EducationHighlightData::from($highlight)
                );
            });

            $data->highlights->each(function ($highlight) use ($education) {
                $this->upsertHighlight(
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

    public function delete(EducationData $educationData): ?EducationData
    {
        $education = Education::find($educationData->id);

        if (! $education) {
            return null;
        }

        $education->highlights->each(function ($highlight) {
            $this->deleteHighlight(
                EducationHighlightData::from($highlight)
            );
        });

        $education->delete();

        return EducationData::from($education);
    }

    public function upsertHighlight(EducationHighlightData $data): EducationHighlightData
    {
        $educationHighlight = EducationHighlight::updateOrCreate(
            ['id' => $data->id],
            [
                'content' => $data->content,
                'sort' => $data->sort ?? 9999,
            ]
        );

        if ($data->education instanceof EducationData && $education = Education::find($data->education?->id)) {
            $educationHighlight->education()->associate($education);
        }

        $educationHighlight->save();

        return EducationHighlightData::from($educationHighlight);
    }

    public function deleteHighlight(EducationHighlightData $educationHighlightData): ?EducationHighlightData
    {
        $educationHighlight = EducationHighlight::find($educationHighlightData->id);

        if (! $educationHighlight) {
            return null;
        }

        $educationHighlight->delete();

        return EducationHighlightData::from($educationHighlight);
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\Resumes\Services;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Support\Collection;

class SubjectsService
{
    public function __construct(
        private SkillsService $skillsService,
        private EducationsService $educationsService,
        private EmployersService $employersService,
    ) {}

    public function upsert(SubjectData $data): SubjectData
    {
        $subject = Subject::updateOrCreate(
            ['id' => $data->id],
            [
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'title' => $data->title,
                'city' => $data->city,
                'state' => $data->state,
                'phone_number' => $data->phone_number,
                'email' => $data->email,
                'overview' => $data->overview,
            ]
        );

        if ($data->user instanceof UserData && $user = User::find($data->user?->id)) {
            $subject->user()->associate($user);
        }

        if ($data->highlights instanceof Collection) {

            $subject->highlights->filter(
                fn ($highlight) => ! $data->highlights->contains('id', $highlight->id)
            )->each(function ($highlight) {
                $this->deleteHighlight(
                    SubjectHighlightData::from($highlight)
                );
            });

            $data->highlights->each(function ($highlight) use ($subject) {
                $this->upsertHighlight(
                    SubjectHighlightData::from([
                        ...$highlight->toArray(),
                        'subject' => $subject,
                    ])
                );
            });
        }

        if ($data->skills instanceof Collection) {

            $subject->skills->filter(
                fn ($skill) => ! $data->skills->contains('id', $skill->id)
            )->each(function ($skill) {
                $this->skillsService->delete(
                    SkillData::from($skill)
                );
            });

            $data->skills->each(function ($skill) use ($subject) {
                $this->skillsService->upsert(
                    SkillData::from([
                        ...$skill->toArray(),
                        'subject' => $subject,
                    ])
                );
            });
        }

        if ($data->employers instanceof Collection) {

            $subject->employers->filter(
                fn ($employer) => ! $data->employers->contains('id', $employer->id)
            )->each(function ($employer) {
                $this->employersService->delete(
                    EmployerData::from($employer)
                );
            });

            $data->employers->each(function ($employer) use ($subject) {
                $this->employersService->upsert(
                    EmployerData::from([
                        ...$employer->toArray(),
                        'subject' => $subject,
                    ])
                );
            });
        }

        if ($data->education instanceof Collection) {

            $subject->education->filter(
                fn ($education) => ! $data->education->contains('id', $education->id)
            )->each(function ($education) {
                $this->educationsService->delete(
                    EducationData::from($education)
                );
            });

            $data->education->each(function ($education) use ($subject) {
                $this->educationsService->upsert(
                    EducationData::from([
                        ...$education->toArray(),
                        'subject' => $subject,
                    ])
                );
            });
        }

        $subject->save();

        return SubjectData::from($subject);
    }

    public function delete(SubjectData $subject): ?SubjectData
    {
        $subject = Subject::find($subject->id);

        if (! $subject) {
            return null;
        }

        $subject->highlights->each(function ($highlight) {
            $this->deleteHighlight(
                SubjectHighlightData::from($highlight)
            );
        });

        $subject->skills->each(function ($skill) {
            $this->skillsService->delete(
                SkillData::from($skill)
            );
        });

        $subject->employers->each(function ($employer) {
            $this->employersService->delete(
                EmployerData::from($employer)
            );
        });

        $subject->education->each(function ($education) {
            $this->educationsService->delete(
                EducationData::from($education)
            );
        });

        $subject->delete();

        return SubjectData::from($subject);
    }

    public function upsertHighlight(SubjectHighlightData $data): SubjectHighlightData
    {
        $subjectHighlight = SubjectHighlight::updateOrCreate(
            ['id' => $data->id],
            [
                'content' => $data->content,
                'sort' => $data->sort ?? 9999,
            ]
        );

        if ($data->subject instanceof SubjectData && $subject = Subject::find($data->subject?->id)) {
            $subjectHighlight->subject()->associate($subject);
        }

        $subjectHighlight->save();

        return SubjectHighlightData::from($subjectHighlight);
    }

    public function deleteHighlight(SubjectHighlightData $subjectHighlightData): ?SubjectHighlightData
    {
        $subjectHighlight = SubjectHighlight::find($subjectHighlightData->id);

        if (! $subjectHighlight) {
            return null;
        }

        $subjectHighlight->delete();

        return SubjectHighlightData::from($subjectHighlight);
    }
}

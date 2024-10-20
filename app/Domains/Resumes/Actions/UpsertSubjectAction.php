<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Support\Collection;

class UpsertSubjectAction
{
    public function __construct(
        private UpsertSubjectHighlightAction $upsertSubjectHighlightAction,
        private DeleteSubjectHighlightAction $deleteSubjectHighlightAction,
        private UpsertSkillAction $upsertSkillAction,
        private DeleteSkillAction $deleteSkillAction,
        private UpsertEmployerAction $upsertEmployerAction,
        private DeleteEmployerAction $deleteEmployerAction,
        private UpsertEducationAction $upsertEducationAction,
        private DeleteEducationAction $deleteEducationAction,
    ) {}

    public function execute(SubjectData $data): SubjectData
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
                $this->deleteSubjectHighlightAction->execute(
                    SubjectHighlightData::from($highlight)
                );
            });

            $data->highlights->each(function ($highlight) use ($subject) {
                $this->upsertSubjectHighlightAction->execute(
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
                $this->deleteSkillAction->execute(
                    SkillData::from($skill)
                );
            });

            $data->skills->each(function ($skill) use ($subject) {
                $this->upsertSkillAction->execute(
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
                $this->deleteEmployerAction->execute(
                    EmployerData::from($employer)
                );
            });

            $data->employers->each(function ($employer) use ($subject) {
                $this->upsertEmployerAction->execute(
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
                $this->deleteEducationAction->execute(
                    EducationData::from($education)
                );
            });

            $data->education->each(function ($education) use ($subject) {
                $this->upsertEducationAction->execute(
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
}

<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Subject;

class UpsertSubjectAction
{
    public function handle(SubjectData $data): SubjectData
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

        if ($data->author) {
            $subject->author()->associate($data->author->id);
        } else {
            $subject->author()->dissociate();
        }

        $subject->save();

        return SubjectData::from($subject);
    }
}

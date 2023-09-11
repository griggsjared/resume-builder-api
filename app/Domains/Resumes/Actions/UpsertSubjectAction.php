<?php

namespace App\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;

class UpsertSubjectAction
{
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

        if ($data->author instanceof UserData && $user = User::find($data->author?->id)) {
            $subject->author()->associate($user);
        }

        $subject->save();

        return SubjectData::from($subject);
    }
}

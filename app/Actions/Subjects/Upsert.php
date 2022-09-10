<?php

namespace App\Actions\Subjects;

use App\DTO\SubjectData;
use App\Models\Subject;

class Upsert
{
    public function execute(SubjectData $data): Subject
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

        if($data->author) {
            $subject->author()->associate($data->author->id);
        } else {
            $subject->author()->dissociate();
        }

        return $subject;
    }
}

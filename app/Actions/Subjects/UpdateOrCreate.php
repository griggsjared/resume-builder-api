<?php

namespace App\Actions\Subjects;

use App\DTO\SubjectData;
use App\Models\Subject;

class UpdateOrCreate
{
    public function execute(SubjectData $data): Subject
    {
        return Subject::updateOrCreate(
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
    }
}

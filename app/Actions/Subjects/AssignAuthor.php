<?php

namespace App\Actions\Subjects;

use App\Models\Subject;
use App\Models\User;

class AssignAuthor
{
    public function execute(Subject $subject, User $user): bool
    {
        $subject->author()->associate($user);

        return $subject->save();
    }
}

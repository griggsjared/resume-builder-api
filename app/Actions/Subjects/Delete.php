<?php

namespace App\Actions\Subjects;

use App\Models\Subject;

class Delete
{
    public function execute(Subject $subject): bool
    {
        return $subject->delete();
    }
}

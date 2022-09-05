<?php

namespace App\Actions\Users;

use App\Models\User;

class Delete
{
    public function execute(User $user): bool
    {
        return $user->delete();
    }
}

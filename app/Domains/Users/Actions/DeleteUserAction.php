<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;

class DeleteUserAction
{
    public function handle(UserData $user): ?UserData
    {
        $user = User::find($user->id);

        if (! $user) {
            return null;
        }

        $user->delete();

        return UserData::from($user);
    }
}

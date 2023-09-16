<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserAccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserAccessToken;

class DeleteUserAction
{
    public function __construct(
        private DeleteUserAccessTokenAction $deleteUserAccessTokenAction,
    ) {
    }

    public function execute(UserData $user): ?UserData
    {
        $user = User::find($user->id);

        if (! $user) {
            return null;
        }

        $user->tokens()->each(function(UserAccessToken $token) {
            $this->deleteUserAccessTokenAction->execute(
                UserAccessTokenData::from($token)
            );
        });

        $user->delete();

        return UserData::from($user);
    }
}

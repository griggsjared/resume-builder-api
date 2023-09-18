<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;

class DeleteUserAction
{
    public function __construct(
        private DeleteAccessTokenAction $deleteAccessTokenAction,
    ) {
    }

    public function execute(UserData $user): ?UserData
    {
        $user = User::find($user->id);

        if (! $user) {
            return null;
        }

        $user->accessTokens->each(function (AccessToken $token) {
            $this->deleteAccessTokenAction->execute(
                AccessTokenData::from($token)
            );
        });

        $user->delete();

        return UserData::from($user);
    }
}

<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserAccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Support\Carbon;

class GenerateUserAccessTokenAction
{
    public function execute(UserData $data, string $name, ?Carbon $expiresAt = null): ?UserAccessTokenData
    {
        $user = User::find($data->id);

        if (! $user) {
            return null;
        }

        $token = $user->createToken(
            name: $name,
            expiresAt: $expiresAt
        );

        return UserAccessTokenData::from([
            ...$token->accessToken->toArray(),
            'token' => $token->plainTextToken,
        ]);
    }
}


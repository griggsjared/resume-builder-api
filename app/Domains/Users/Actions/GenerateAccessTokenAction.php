<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Support\Carbon;

class GenerateAccessTokenAction
{
    public function execute(UserData $data, string $name, Carbon $expiresAt = null): ?AccessTokenData
    {
        $user = User::find($data->id);

        if (! $user) {
            return null;
        }

        $token = $user->createToken(
            name: $name,
            expiresAt: $expiresAt
        );

        $tokenParts = explode('|', $token->plainTextToken);

        return AccessTokenData::from([
            ...$token->accessToken->toArray(),
            'access_token' => $tokenParts[1],
        ]);
    }
}

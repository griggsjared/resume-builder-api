<?php

declare(strict_types=1);

namespace App\Domains\Users\Services;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use Illuminate\Support\Carbon;

class AccessTokensService
{
    public function generate(UserData $data, string $name, ?Carbon $expiresAt = null): ?AccessTokenData
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

    public function refresh(AccessTokenData $data, Carbon $expiresAt): ?AccessTokenData
    {
        $token = AccessToken::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('id', $data->id)
            ->first();

        if (! $token) {
            return null;
        }

        $token->expires_at = now();
        $token->save();

        return $this->generate(
            UserData::from($token->tokenable),
            $token->name,
            $expiresAt
        );
    }

    public function delete(AccessTokenData $data): ?AccessTokenData
    {
        $accessToken = AccessToken::find($data->id);

        if (! $accessToken) {
            return null;
        }

        $accessToken->delete();

        return AccessTokenData::from($accessToken);
    }
}

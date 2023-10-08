<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\AccessToken;
use Illuminate\Support\Carbon;

class RefreshAccessTokenAction
{
    public function __construct(
        private DeleteAccessTokenAction $deleteAccessTokenAction,
        private GenerateAccessTokenAction $generateAccessTokenAction
    ) {
    }

    public function execute(AccessTokenData $data, Carbon $expiresAt): ?AccessTokenData
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

        return $this->generateAccessTokenAction->execute(
            UserData::from($token->tokenable),
            $token->name,
            $expiresAt
        );
    }
}

<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Models\AccessToken;
use Illuminate\Support\Carbon;

class ExtendAccessTokenAction
{
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

        $token->expires_at = $expiresAt;
        $token->save();

        return AccessTokenData::from($token);
    }
}

<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserAccessTokenData;
use App\Domains\Users\Models\UserAccessToken;
use Illuminate\Support\Carbon;

class ExtendUserAccessTokenAction
{
    public function execute(UserAccessTokenData $data, Carbon $expiresAt): ?UserAccessTokenData
    {
        $token = UserAccessToken::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('id', $data->id)
            ->first();

        if (! $token || $expiresAt->isBefore($token->expires_at)) {
            return null;
        }

        $token->expires_at = $expiresAt;
        $token->save();

        return UserAccessTokenData::from($token);
    }
}

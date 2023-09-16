<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserAccessTokenData;
use App\Domains\Users\Models\UserAccessToken;

class DeleteUserAccessTokenAction
{
    public function execute(UserAccessTokenData $data): ?UserAccessTokenData
    {
        $accessToken = UserAccessToken::find($data->id);

        if (! $accessToken) {
            return null;
        }

        $accessToken->delete();

        return UserAccessTokenData::from($accessToken);
    }
}

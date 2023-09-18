<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Models\AccessToken;

class DeleteAccessTokenAction
{
    public function execute(AccessTokenData $data): ?AccessTokenData
    {
        $accessToken = AccessToken::find($data->id);

        if (! $accessToken) {
            return null;
        }

        $accessToken->delete();

        return AccessTokenData::from($accessToken);
    }
}

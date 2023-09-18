<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Actions\ExtendAccessTokenAction;
use App\Domains\Users\Data\AccessTokenData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\ViewData\AccessTokenData as AccessTokenViewData;
use Illuminate\Http\JsonResponse;

class RefreshController extends Controller
{
    public function __construct(
        private ExtendAccessTokenAction $extendAccessTokenAction
    ) {
    }

    public function __invoke(RefreshRequest $request): JsonResponse
    {
        $accessToken = $request->accessToken();

        return response()->json(
            AccessTokenViewData::from([
                ...$this->extendAccessTokenAction->execute(
                    AccessTokenData::from($accessToken),
                    now()->addSeconds(config('auth.token_expiration', 3600))
                )->toArray(),
                'token' => $request->bearerToken(),
                'type' => 'bearer',
            ])
        );
    }
}

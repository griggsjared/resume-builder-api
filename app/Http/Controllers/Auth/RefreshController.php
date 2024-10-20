<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Actions\RefreshAccessTokenAction;
use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Services\AccessTokensService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\ApiData\AccessTokenApiData;
use Illuminate\Http\JsonResponse;

class RefreshController extends Controller
{
    public function __construct(
        private AccessTokensService $accessTokensService,
    ) {}

    public function __invoke(RefreshRequest $request): JsonResponse
    {
        $accessToken = $request->accessToken();

        return response()->json(
            AccessTokenApiData::from([
                ...$this->accessTokensService->refresh(
                    AccessTokenData::from($accessToken),
                    now()->addSeconds(config('auth.token_expiration', 3600))
                )->toArray(),
                'type' => 'bearer',
            ])
        );
    }
}

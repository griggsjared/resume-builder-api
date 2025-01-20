<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Services\AccessTokensService;
use App\Http\ApiData\AccessTokenApiData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __construct(
        private AccessTokensService $accessTokensService,
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        return response()->json(
            AccessTokenApiData::from([
                ...$this->accessTokensService->generate(
                    $request->authenticatedUserData(),
                    'login-token',
                    now()->addSeconds(config('auth.token_expiration', 3600))
                )->toArray(),
                'type' => 'bearer',
            ])
        );
    }
}

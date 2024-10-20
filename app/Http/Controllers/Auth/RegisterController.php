<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Services\AccessTokensService;
use App\Domains\Users\Services\UsersService;
use App\Http\ApiData\AccessTokenApiData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(
        private UsersService $usersService,
        private AccessTokensService $accessTokensService,
    ) {}

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $userData = $this->usersService->upsert(
            UserData::from([
                ...$request->validated(),
                'role' => UserRole::Basic,
            ]),
        );

        return response()->json(
            AccessTokenApiData::from([
                ...$this->accessTokensService->generate(
                    $userData,
                    'login-token',
                    now()->addSeconds(config('auth.token_expiration', 3600))
                )->toArray(),
                'type' => 'bearer',
            ])
        );
    }
}

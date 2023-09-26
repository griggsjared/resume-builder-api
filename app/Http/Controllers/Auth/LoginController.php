<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Actions\GenerateAccessTokenAction;
use App\Domains\Users\Data\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\ViewData\AccessTokenViewData;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __construct(
        private GenerateAccessTokenAction $generateAccessTokenAction
    ) {
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticatedUser();

        return response()->json(
            AccessTokenViewData::from([
                ...$this->generateAccessTokenAction->execute(
                    UserData::from($user),
                    "login-token-{$user->id}",
                    now()->addSeconds(config('auth.token_expiration', 3600))
                )->toArray(),
                'type' => 'bearer',
            ])
        );
    }
}

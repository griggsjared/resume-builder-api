<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Actions\GenerateAccessTokenAction;
use App\Domains\Users\Actions\UpsertUserAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\ViewData\AccessTokenViewData;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(
        private UpsertUserAction $upsertUserAction,
        private GenerateAccessTokenAction $generateAccessTokenAction
    ) {
    }

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $userData = $this->upsertUserAction->execute(
            UserData::from([
                ...$request->validated(),
                'role' => UserRole::Basic
            ]),
        );

        return response()->json(
            AccessTokenViewData::from([
                ...$this->generateAccessTokenAction->execute(
                    $userData,
                    "login-token-{$userData->id}",
                    now()->addSeconds(config('auth.token_expiration', 3600))
                )->toArray(),
                'type' => 'bearer',
            ])
        );
    }
}

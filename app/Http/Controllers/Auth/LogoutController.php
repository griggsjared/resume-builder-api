<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Users\Actions\DeleteAccessTokenAction;
use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Services\AccessTokensService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogoutRequest;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(
        private AccessTokensService $accessTokensService,
    ) {}

    public function __invoke(LogoutRequest $request): JsonResponse
    {
        $accessToken = $request->accessToken();

        $this->accessTokensService->delete(
            AccessTokenData::from($accessToken)
        );

        return response()->json([
            'message' => 'Ok',
        ], 200);
    }
}

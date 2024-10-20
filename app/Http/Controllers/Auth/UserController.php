<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\ApiData\UserApiData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            UserApiData::from($request->user())
        );
    }
}

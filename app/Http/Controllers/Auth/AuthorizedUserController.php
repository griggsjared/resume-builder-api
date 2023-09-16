<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\ViewData\UserData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorizedUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UserData::from($request->user()),
        ]);
    }
}

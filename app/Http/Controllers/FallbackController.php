<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class FallbackController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'message' => '404: Not Found',
        ], 404);
    }
}

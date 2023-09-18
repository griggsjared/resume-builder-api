<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domains\Users\Models\AccessToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LogoutRequest extends FormRequest
{
    /**
     * @throws ValidationException
     */
    public function accessToken(): AccessToken
    {
        $accessToken = $this->user()->currentAccessToken();

        if (! $accessToken->expires_at) {
            throw ValidationException::withMessages([
                'token' => 'The current access token cannot be used to logout.',
            ]);
        }

        return $accessToken;
    }
}

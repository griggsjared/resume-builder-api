<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Models\AccessToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LogoutRequest extends FormRequest
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->accessToken()->expires_at === null) {
                $validator->errors()->add('token', 'The current access token cannot be used to logout.');
            }
        });
    }

    public function accessToken(): AccessToken
    {
        return $this->user()->currentAccessToken();
    }

    public function accessTokenData(): AccessTokenData
    {
        return AccessTokenData::from(
            $this->accessToken()
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (Auth::once($this->only('email', 'password')) === false) {
                $validator->errors()->add('email', 'Login credentials were incorrect.');
            }
        });
    }

    public function authenticatedUser(): ?User
    {
        return Auth::getUser();
    }

    public function authenticatedUserData(): UserData
    {
        return UserData::from($this->authenticatedUser());
    }
}

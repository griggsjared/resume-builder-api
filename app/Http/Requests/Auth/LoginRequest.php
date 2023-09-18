<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    /**
     * @throws ValidationException
     */
    public function authenticatedUser(): User
    {
        if (! Auth::once($this->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => 'Login credentials were incorrect.',
            ]);
        }

        return Auth::getUser();
    }
}

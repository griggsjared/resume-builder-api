<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255'],
        ];

        if ($this->user()->can('changeRole', User::class)) {
            $rules['role'] = ['nullable', 'string', Rule::in(collect(UserRole::cases())->pluck('value'))];
        }

        return $rules;
    }

    public function assignRole(): UserRole
    {
        if ($this->user()->cannot('changeRole', User::class) || $this->has('role') === false) {
            return UserRole::Basic;
        }

        return UserRole::from($this->input('role'));
    }

    public function userData(): UserData
    {
        return UserData::from([
            ...$this->validated(),
            'role' => $this->assignRole(),
        ]);
    }
}

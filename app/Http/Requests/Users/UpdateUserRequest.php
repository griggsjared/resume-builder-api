<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->updatingUser());
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->updatingUser()->id],
            'password' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->user()->can('changeRole', User::class)) {
            $rules['role'] = ['nullable', 'string', Rule::in(collect(UserRole::cases())->pluck('value'))];
        }

        return $rules;
    }

    public function assignRole(): UserRole
    {
        if ($this->user()->cannot('changeRole', User::class)) {
            return UserRole::Basic;
        }

        if ($this->has('role') === false) {
            return $this->updatingUser()->role;
        }

        return UserRole::from($this->input('role'));
    }

    public function userData(): UserData
    {
        return UserData::from([
            ...$this->updatingUser()->toArray(),
            ...$this->validated(),
            'role' => $this->assignRole(),
        ]);
    }

    public function updatingUser(): User
    {
        /** @var User $user */
        $user = $this->route('user');

        return $user;
    }
}

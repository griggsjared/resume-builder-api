<?php

namespace App\Domains\Users\Policies;

use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $model): bool
    {
        if($user->role === UserRole::Admin) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function update(User $user, User $model): bool
    {
        if($user->role === UserRole::Admin) {
            return true;
        }

        if ($user->id !== $model->id) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        return $user->role === UserRole::Admin;
    }

    public function changeRole(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }
}

<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function view(User $user, User $model): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function create(User $user): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function update(User $user, User $model): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }
}

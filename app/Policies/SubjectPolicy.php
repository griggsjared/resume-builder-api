<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Subject $model): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        return $user->id === $model->author_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Subject $model): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        return $user->id === $model->author_id;
    }

    public function delete(User $user, Subject $model): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        return $user->id === $model->author_id;
    }
}

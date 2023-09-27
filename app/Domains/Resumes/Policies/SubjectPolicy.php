<?php

namespace App\Domains\Resumes\Policies;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function view(User $user, Subject $model): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->id === $model->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Subject $model): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->id === $model->user_id;
    }

    public function delete(User $user, Subject $model): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->id === $model->user_id;
    }

    public function assignUser(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }
}

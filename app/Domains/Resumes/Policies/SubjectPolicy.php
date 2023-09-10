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
        return $user->role === UserRole::SuperAdmin;
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

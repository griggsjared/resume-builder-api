<?php

namespace App\Domains\Users\Models;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Enums\UserRole;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasApiTokens<AccessToken> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'email',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
    ];

    /** @var array<string, class-string | string> */
    protected $casts = [
        'role' => UserRole::class,
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'user_id');
    }

    public function accessTokens(): MorphMany
    {
        return $this->tokens();
    }

    protected function scopeAuthorized(Builder $query, User $user): Builder
    {
        if ($user->role === UserRole::Admin) {
            return $query;
        }

        return $query->where('id', $user->id);
    }

    protected function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('email', 'like', "%{$search}%");
        });
    }
}

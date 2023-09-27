<?php

namespace App\Domains\Resumes\Models;

use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory, HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'title',
        'city',
        'state',
        'phone_number',
        'email',
        'overview',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(SubjectHighlight::class);
    }

    public function employers(): HasMany
    {
        return $this->hasMany(Employer::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    protected function scopeAuthorized(Builder $query, User $user): Builder
    {
        if($user->role === UserRole::Admin) {
            return $query;
        }

        return $query->whereHas('user', fn (Builder $query) => $query->where('id', $user->id));
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => trim($attributes['first_name'].' '.$attributes['last_name'])
        );
    }
}

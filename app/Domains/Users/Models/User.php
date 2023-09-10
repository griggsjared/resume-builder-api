<?php

namespace App\Domains\Users\Models;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * @var array<string, class-string | string>
     */
    protected $casts = [
        'role' => UserRole::class,
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'author_id');
    }

    public function fullName(): Attribute
    {
        return Attribute::get(fn ($value, $attributes): string => trim($attributes['first_name'].' '.$attributes['last_name']));
    }

    public function roleLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->role->label());
    }
}

<?php

namespace App\Models;

use App\Models\Enums\UserRole;
use App\Models\Traits\HasUuid;
use Illuminate\Notifications\Notifiable;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
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
        'uuid' => EfficientUuid::class,
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

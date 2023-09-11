<?php

namespace App\Domains\Users\Models;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Users\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
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
}

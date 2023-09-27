<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class AccessToken extends SanctumPersonalAccessToken
{
    use HasFactory, HasUuids;

    /**
     * @var string
     */
    protected $table = 'personal_access_tokens';

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'expires_in',
    ];

    protected function expiresIn(): Attribute
    {
        return Attribute::get(
            fn (): ?int => $this->expires_at?->diffInSeconds(now()->subSecond())
        );
    }
}

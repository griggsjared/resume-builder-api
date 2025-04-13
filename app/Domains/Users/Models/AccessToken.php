<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken;

class AccessToken extends PersonalAccessToken
{
    use HasFactory, HasUuids;

    /** @var string */
    protected $table = 'personal_access_tokens';

    /** list<string> */
    protected $appends = [
        'expires_in',
    ];

    protected function expiresIn(): Attribute
    {
        return Attribute::get(
            fn (): int => -1 * $this->expires_at?->diffInSeconds(now()->subSecond())
        );
    }
}

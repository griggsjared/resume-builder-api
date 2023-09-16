<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class UserAccessToken extends SanctumPersonalAccessToken
{
    use HasUuids, HasFactory;

    /**
     * @var string
     */
    protected $table = 'personal_access_tokens';
}

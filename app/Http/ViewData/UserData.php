<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use App\Http\ViewData\UserRoleData;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly UserRoleData $role
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Spatie\LaravelData\Data;

class UserViewData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly UserRoleViewData $role
    ) {
    }
}

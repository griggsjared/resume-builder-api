<?php

declare(strict_types=1);

namespace App\Http\ApiData;

use Spatie\LaravelData\Data;

class UserApiData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly UserRoleApiData $role
    ) {}
}

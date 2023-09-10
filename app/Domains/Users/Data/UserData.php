<?php

namespace App\Domains\Users\Data;

use App\Domains\Users\Enums\UserRole;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        #[Uuid]
        public ?string $id,
        public string $first_name,
        public string $last_name,
        public string $email,
        #[WithCast(EnumCast::class)]
        public UserRole $role,
        public ?string $password
    ) {
    }
}

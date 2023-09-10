<?php

namespace App\Domains\Users\Data;

use App\Models\Enums\UserRole;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public ?int $id,
        public string $first_name,
        public string $last_name,
        public string $email,
        #[WithCast(EnumCast::class)]
        public UserRole $role,
        public ?string $password
    ) {
    }
}

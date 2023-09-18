<?php

namespace App\Domains\Users\Data;

use App\Domains\Support\Data\Casts\AsCarbonCast;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AccessTokenData extends Data
{
    public function __construct(
        #[Uuid]
        public readonly ?string $id,
        public readonly string $name,
        public readonly Optional|string $token,
        #[WithCast(AsCarbonCast::class)]
        public readonly ?Carbon $expires_at,
        public readonly ?int $expires_in,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AccessTokenData extends Data
{
    public function __construct(
        public readonly Optional|string $token,
        public readonly ?int $expires_in,
        public readonly string $type,
    ) {
    }
}

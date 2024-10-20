<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Spatie\LaravelData\Data;

class AccessTokenViewData extends Data
{
    public function __construct(
        public readonly string $access_token,
        public readonly ?int $expires_in,
        public readonly string $type,
    ) {}
}

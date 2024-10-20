<?php

declare(strict_types=1);

namespace App\Http\ApiData;

use Spatie\LaravelData\Data;

class AccessTokenApiData extends Data
{
    public function __construct(
        public readonly string $access_token,
        public readonly ?int $expires_in,
        public readonly string $type,
    ) {}
}

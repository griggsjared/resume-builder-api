<?php

declare(strict_types=1);

namespace App\Http\ApiData;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class EmployerApiData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $city,
        public readonly string $state,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $started_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $ended_at,
        public readonly bool $is_current
    ) {}
}

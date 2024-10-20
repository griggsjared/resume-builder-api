<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class EducationViewData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $major_degree,
        public readonly bool $earned_major_degree,
        public readonly ?string $minor_degree,
        public readonly bool $earned_minor_degree,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $started_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $ended_at,
        public readonly bool $is_current,
    ) {}
}

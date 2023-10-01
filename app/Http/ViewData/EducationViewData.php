<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use App\Domains\Support\Data\Casts\AsCarbonCast;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
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
        #[WithCast(AsCarbonCast::class)]
        public readonly ?Carbon $started_at,
        #[WithCast(AsCarbonCast::class)]
        public readonly ?Carbon $ended_at,
        public readonly bool $is_current,
    ) {
    }
}

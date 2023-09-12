<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Support\Data\Casts\AsCarbonCast;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class EmployerData extends Data
{
    public function __construct(
        #[Uuid]
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $city,
        public readonly ?string $state,
        #[WithCast(AsCarbonCast::class)]
        public readonly ?Carbon $started_at,
        #[WithCast(AsCarbonCast::class)]
        public readonly ?Carbon $ended_at,
        public readonly Optional|SubjectData $subject,
        #[DataCollectionOf(EmployerHighlightData::class)]
        public readonly Optional|DataCollection $highlights,
    ) {
    }
}

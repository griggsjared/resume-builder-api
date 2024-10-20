<?php

namespace App\Domains\Resumes\Data;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EducationData extends Data
{
    public function __construct(
        #[Uuid]
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $major_degree,
        public readonly ?bool $earned_major_degree,
        public readonly ?string $minor_degree,
        public readonly ?bool $earned_minor_degree,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $started_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $ended_at,
        public readonly Optional|SubjectData $subject,
        /**
         * @var Optional|Collection<int, EducationHighlightData>
         */
        public readonly Optional|Collection $highlights,
    ) {}
}

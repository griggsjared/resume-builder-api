<?php

namespace App\Domains\Resumes\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EducationHighlightData extends Data
{
    public function __construct(
        #[Uuid]
        public readonly ?string $id,
        public readonly string $content,
        public readonly ?int $sort,
        public readonly Optional|EducationData $education
    ) {}
}

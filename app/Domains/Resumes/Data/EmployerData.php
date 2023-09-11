<?php

namespace App\Domains\Resumes\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EmployerData extends Data
{
    public function __construct(
        #[Uuid]
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $category,
        public readonly ?int $sort,
        public readonly Optional|SubjectData $subject
    ) {
    }
}

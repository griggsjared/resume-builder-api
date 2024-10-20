<?php

declare(strict_types=1);

namespace App\Http\ApiData;

use Spatie\LaravelData\Data;

class SkillApiData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $category,
        public readonly int $sort,
    ) {}
}

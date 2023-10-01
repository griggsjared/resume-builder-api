<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Spatie\LaravelData\Data;

class SkillViewData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $category,
        public readonly int $sort,
    ) {
    }
}

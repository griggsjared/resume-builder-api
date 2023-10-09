<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Spatie\LaravelData\Data;

class SubjectHighlightViewData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $content,
        public readonly int $sort,
    ) {
    }
}

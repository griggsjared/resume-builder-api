<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Resumes\Models\EmployerHighlight;
use Spatie\LaravelData\Data;

class EmployerHighlightData extends Data
{
    public function __construct(
        public ?int $id,
        public string $content,
        public ?EmployerData $employer = null
    ) {
    }

    public static function fromModel(EmployerHighlight $highlight): self
    {
        return self::from([
            ...$highlight->toArray(),
            'employer' => $highlight->employer,
        ]);
    }
}

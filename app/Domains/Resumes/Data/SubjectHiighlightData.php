<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Resumes\Models\SubjectHighlight;
use Spatie\LaravelData\Data;

class SubjectHighlightData extends Data
{
    public function __construct(
        public ?int $id,
        public string $content,
        public ?SubjectData $subject = null
    ) {
    }

    public static function fromModel(SubjectHighlight $highlight): self
    {
        return self::from([
            ...$highlight->toArray(),
            'subject' => $highlight->subject,
        ]);
    }
}

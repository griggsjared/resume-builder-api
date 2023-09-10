<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Resumes\Models\Subject;
use Spatie\LaravelData\Data;

class SubjectData extends Data
{
    public function __construct(
        public ?int $id,
        public string $first_name,
        public string $last_name,
        public ?string $title,
        public ?string $city,
        public ?string $state,
        public ?string $phone_number,
        public ?string $email,
        public ?string $overview,
        public ?UserData $author = null
    ) {
    }

    public static function fromModel(Subject $subject): self
    {
        return self::from([
            ...$subject->toArray(),
            'author' => $subject->author,
        ]);
    }
}

<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Resumes\Models\Employer;
use Spatie\LaravelData\Data;

class EmployerData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $category,
        public ?int $sort = null,
        public ?SubjectData $subject = null
    ) {
    }

    public static function fromModel(Employer $employer): self
    {
        return self::from([
            ...$employer->toArray(),
            'subject' => $employer->subject,
        ]);
    }
}

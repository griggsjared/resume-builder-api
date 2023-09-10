<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Resumes\Models\Skill;
use Spatie\LaravelData\Data;

class SkillData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $category,
        public ?int $sort = null,
        public ?SubjectData $subject = null
    ) {
    }

    public static function fromModel(Skill $skill): self
    {
        return self::from([
            ...$skill->toArray(),
            'subject' => $skill->subject,
        ]);
    }
}

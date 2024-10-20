<?php

namespace App\Domains\Resumes\Data;

use App\Domains\Users\Data\UserData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SubjectData extends Data
{
    public function __construct(
        #[Uuid]
        public readonly ?string $id,
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $title,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $phone_number,
        public readonly ?string $email,
        public readonly ?string $overview,
        public readonly Optional|UserData $user,
        /**
         * @var Optional|Collection<int, SubjectHighlightData>
         */
        public readonly Optional|Collection $highlights,
        /**
         * @var Optional|Collection<int, SkillData>
         */
        public readonly Optional|Collection $skills,
        /**
         * @var Optional|Collection<int, EmployerData>
         */
        public readonly Optional|Collection $employers,
        /**
         * @var Optional|Collection<int, EducationData>
         */
        public readonly Optional|Collection $education,
    ) {}
}

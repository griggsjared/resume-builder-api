<?php

namespace App\DTO;

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
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Spatie\LaravelData\Data;

class SubjectViewData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $title,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $phone_number,
        public readonly ?string $email,
        public readonly ?string $overview,
    ) {}
}

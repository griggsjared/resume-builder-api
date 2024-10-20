<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use App\Domains\Users\Enums\UserRole;
use Spatie\LaravelData\Data;

class UserRoleViewData extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly string $label,
    ) {}

    public static function fromEnum(UserRole $enum): self
    {
        return new self($enum->value, $enum->label());
    }

    public static function fromString(string $role): self
    {
        $enum = UserRole::tryFrom($role);

        if (is_null($enum)) {
            return self::optional(null);
        }

        return new self($enum->value, $enum->label());
    }
}

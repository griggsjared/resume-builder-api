<?php

namespace App\Domains\Users\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case Basic = 'basic';

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Basic => 'Basic',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::SuperAdmin => 10,
            self::Basic => 5,
        };
    }
}

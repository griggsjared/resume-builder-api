<?php

namespace App\Domains\Users\Enums;

use App\Domains\Support\Enum\Interfaces\HasLabelInterface;

enum UserRole: string implements HasLabelInterface
{
    case Admin = 'admin';
    case Basic = 'basic';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Basic => 'Basic',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\Support\Enum\Interfaces;

interface HasLabelInterface
{
    public function label(): string;
}

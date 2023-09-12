<?php

namespace App\Domains\Support\Data\Casts;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class AsCarbonCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context = []): Carbon
    {
        return Carbon::parse($value);
    }
}

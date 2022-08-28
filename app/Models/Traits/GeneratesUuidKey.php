<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait GeneratesUuidKey
{
    public function uuidKeyColumn(): string
    {
        return 'uuid';
    }

    public static function bootGeneratesUuidKey(): void
    {
        static::creating(function ($model) {
            $model->generateUuidKey();
        });
    }

    public function generateUuidKey(): void
    {
        $this->{$this->uuidKeyColumn()} = Str::orderedUuid();
    }
}

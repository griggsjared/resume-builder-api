<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait GeneratesUuidKey
{
    /**
     * @return string
     */
    public function uuidKeyColumn() : string
    {
        return 'uuid';
    }

    /**
     * @return void
     */
    public static function bootGeneratesUuidKey() : void
    {
        static::creating(function($model) {
            $model->generateUuidKey();
        });
    }

    /**
     * @return void
     */
    public function generateUuidKey() : void
    {
        $this->{$this->uuidKeyColumn()} = Str::orderedUuid();
    }
}

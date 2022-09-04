<?php

namespace App\Models\Traits;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait HasUuid
{
    use GeneratesUuid;

    protected $uuidVersion = 'ordered';

    public function uuidColumn(): string
    {
        return 'uuid';
    }

    /**
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (Uuid::isValid($value)) {
            return $this->whereUuid($value)->first();
        }

        return $this->resolveRouteBindingQuery($this, $value, $field)->first();
    }
}

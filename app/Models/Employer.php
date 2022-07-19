<?php

namespace App\Models;

use App\Models\Traits\GeneratesUuidKey;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    use HasFactory, GeneratesUuidKey;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'started_at',
        'ended_at'
    ];

    /**
     * @return BelongsTo
     */
    public function subject() :BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return HasMany
     */
    public function highlights() : HasMany
    {
        return $this->hasMany(EmployerHighlight::class);
    }

    /**
     * @return Attribute
     */
    protected function isCurrent() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => is_null($attributes['ended_at'])
        );
    }
}

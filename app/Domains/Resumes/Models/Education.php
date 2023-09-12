<?php

namespace App\Domains\Resumes\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Education extends Model
{
    use HasFactory, HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'city',
        'state',
        'degree',
    ];

    /**
     * @var array<int, string>
     */
    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(EducationHighlight::class);
    }

    protected function isCurrent(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => is_null($attributes['ended_at'])
        );
    }
}

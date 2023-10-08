<?php

namespace App\Domains\Resumes\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
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
        'major_degree',
        'minor_degree',
    ];

    /**
     * @var array<int, string>
     */
    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'earned_major_degree' => 'boolean',
        'earned_minor_degree' => 'boolean',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'is_current',
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

    protected function earnedMajorDegree(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => ! is_null($attributes['major_degree']) && $attributes['earned_major_degree']
        );
    }

    protected function earnedMinorDegree(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => ! is_null($attributes['minor_degree']) && $attributes['earned_minor_degree']
        );
    }

    protected function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('state', 'like', "%{$search}%")
                ->orWhere('major_degree', 'like', "%{$search}%")
                ->orWhere('minor_degree', 'like', "%{$search}%");
        });
    }
}

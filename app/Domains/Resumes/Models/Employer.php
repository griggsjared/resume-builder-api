<?php

namespace App\Domains\Resumes\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    use HasFactory, HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'city',
        'state',
    ];

    /**
     * @var array<int, string>
     */
    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
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
        return $this->hasMany(EmployerHighlight::class);
    }

    protected function isCurrent(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => is_null($attributes['ended_at'])
        );
    }

    protected function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('state', 'like', "%{$search}%");
        });
    }
}

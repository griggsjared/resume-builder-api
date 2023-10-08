<?php

namespace App\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Education;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationHighlight extends Model
{
    use HasFactory, HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
    ];

    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class);
    }

    protected function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('content', 'like', "%{$search}%");
        });
    }
}

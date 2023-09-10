<?php

namespace App\Domains\Resumes\Models;

use App\Domains\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory, HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

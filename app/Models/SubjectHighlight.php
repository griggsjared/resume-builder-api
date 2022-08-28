<?php

namespace App\Models;

use App\Models\Traits\GeneratesUuidKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectHighlight extends Model
{
    use HasFactory, GeneratesUuidKey;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

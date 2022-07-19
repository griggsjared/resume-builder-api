<?php

namespace App\Models;

use App\Models\Traits\GeneratesUuidKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerHighlight extends Model
{
    use HasFactory, GeneratesUuidKey;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content'
    ];

    /**
     * @return BelongsTo
     */
    public function employer() : BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}

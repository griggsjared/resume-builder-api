<?php

namespace App\Domains\Resumes\Models;

use App\Domains\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectImage extends Model
{
    use HasFactory, Imageable, HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [];

    public function imageStorageDisk(): string
    {
        return 'public';
    }

    public function imageBaseDirectory(): string
    {
        return 'content-images';
    }

    /**
     * @return array<string, <string, mixed>
     */
    public function imageDims(): array
    {
        return [
            'display' => [
                'width' => 800,
                'height' => null,
                'quality' => 70,
            ],
            'thumbnail' => [
                'width' => 400,
                'height' => 300,
                'quality' => 70,
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function imageEncodings(): array
    {
        return ['webp', 'jpg'];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

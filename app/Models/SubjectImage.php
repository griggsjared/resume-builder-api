<?php

namespace App\Models;

use App\Models\Traits\Imageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectImage extends Model
{
    use HasFactory, Imageable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The storage disk for the imageable storage driver.
     *
     * @return string
     */
    public function imageStorageDisk() : string
    {
        return 'public';
    }

    /**
     * The base directory for the uploaded images.
     *
     * @return string
     */
    public function imageBaseDirectory() : string
    {
        return 'content-images';
    }

    /**
     * @return array
     */
    public function imageDims() : array
    {
        return [
            'display' => [
                'width' => 800,
                'height' => null,
                'quality' => 70
            ],
            'thumbnail' => [
                'width' => 400,
                'height' => 300,
                'quality' => 70
            ]
        ];
    }

    /**
     * @return array
     */
    public function imageEncodings() : array
    {
        return ['webp', 'jpg'];
    }

    /**
     * @return BelongsTo
     */
    public function subject() : BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

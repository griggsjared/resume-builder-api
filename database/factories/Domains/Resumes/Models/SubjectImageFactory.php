<?php

namespace Database\Factories\Domains\Resumes\Models;

use App\Domains\Resumes\Models\SubjectImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectImage>
 */
class SubjectImageFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = SubjectImage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'filename' => $this->faker->image(),
        ];
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Subject;
use App\Models\SubjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SubjectImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('subject_images', [
                'id',
                'uuid',
                'subject_id',
                'filename',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $subject = SubjectImage::factory()->create();

        $this->assertTrue(
            Uuid::isValid($subject->uuid)
        );
    }

    /** @test */
    public function has_relationships()
    {
        $image = SubjectImage::factory()
            ->for(Subject::factory(), 'subject')
            ->create();

        $this->assertInstanceOf(SubjectImage::class, $image);
        $this->assertInstanceOf(Subject::class, $image->subject);
    }

    /** @test */
    public function has_image_encodings_defined()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsArray($image->imageEncodings());
    }

    /** @test */
    public function has_image_storage_disk_defined()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsString($image->imageStorageDisk());
    }

    /** @test */
    public function has_image_base_directory_defined()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsString($image->imageBaseDirectory());
    }

    /** @test */
    public function has_image_dims_defined()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsArray($image->imageDims());
        $this->assertNotEmpty($image->imageDims());
        $this->assertArrayHasKey('width', collect($image->imageDims())->first());
        $this->assertArrayHasKey('height', collect($image->imageDims())->first());
        $this->assertArrayHasKey('quality', collect($image->imageDims())->first());
    }
}

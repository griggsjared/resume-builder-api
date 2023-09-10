<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubjectImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('subject_images', 'id'));
        $this->assertTrue(Schema::hasColumn('subject_images', 'subject_id'));
        $this->assertTrue(Schema::hasColumn('subject_images', 'filename'));
        $this->assertTrue(Schema::hasColumn('subject_images', 'created_at'));
        $this->assertTrue(Schema::hasColumn('subject_images', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $image = SubjectImage::factory()
            ->for(Subject::factory(), 'subject')
            ->create();

        $this->assertInstanceOf(SubjectImage::class, $image);
        $this->assertInstanceOf(Subject::class, $image->subject);
    }

    /** @test */
    public function it_defines_the_image_decodings()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsArray($image->imageEncodings());
    }

    /** @test */
    public function it_defines_the_storage_disk()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsString($image->imageStorageDisk());
    }

    /** @test */
    public function it_defines_the_image_base_directory()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsString($image->imageBaseDirectory());
    }

    /** @test */
    public function it_defines_the_images_dims()
    {
        $image = SubjectImage::factory()->create();

        $this->assertIsArray($image->imageDims());
        $this->assertNotEmpty($image->imageDims());
        $this->assertArrayHasKey('width', collect($image->imageDims())->first());
        $this->assertArrayHasKey('height', collect($image->imageDims())->first());
        $this->assertArrayHasKey('quality', collect($image->imageDims())->first());
    }
}

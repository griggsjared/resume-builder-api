<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EducationHighlightTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('education_highlights', 'id'));
        $this->assertTrue(Schema::hasColumn('education_highlights', 'education_id'));
        $this->assertTrue(Schema::hasColumn('education_highlights', 'content'));
        $this->assertTrue(Schema::hasColumn('education_highlights', 'created_at'));
        $this->assertTrue(Schema::hasColumn('education_highlights', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $highlight = EducationHighlight::factory()
            ->for(Education::factory(), 'education')
            ->create();

        $this->assertInstanceOf(EducationHighlight::class, $highlight);
        $this->assertInstanceOf(Education::class, $highlight->education);
    }
}

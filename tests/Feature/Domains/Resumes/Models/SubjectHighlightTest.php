<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubjectHighlightTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('subject_highlights', 'id'));
        $this->assertTrue(Schema::hasColumn('subject_highlights', 'subject_id'));
        $this->assertTrue(Schema::hasColumn('subject_highlights', 'content'));
        $this->assertTrue(Schema::hasColumn('subject_highlights', 'created_at'));
        $this->assertTrue(Schema::hasColumn('subject_highlights', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $highlight = SubjectHighlight::factory()
            ->for(Subject::factory(), 'subject')
            ->create();

        $this->assertInstanceOf(SubjectHighlight::class, $highlight);
        $this->assertInstanceOf(Subject::class, $highlight->subject);
    }
}

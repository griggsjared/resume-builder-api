<?php

namespace Tests\Feature\Models;

use App\Models\Subject;
use App\Models\SubjectHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SubjectHighlightTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('subject_highlights', [
                'id',
                'uuid',
                'subject_id',
                'content',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $highlight = SubjectHighlight::factory()->create();

        $this->assertTrue(
            Uuid::isValid($highlight->uuid)
        );
    }

    /** @test */
    public function has_relationships()
    {
        $highlight = SubjectHighlight::factory()
            ->for(Subject::factory(), 'subject')
            ->create();

        $this->assertInstanceOf(SubjectHighlight::class, $highlight);
        $this->assertInstanceOf(Subject::class, $highlight->subject);
    }
}

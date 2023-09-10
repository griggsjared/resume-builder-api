<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmployerHighlightTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('employer_highlights', 'id'));
        $this->assertTrue(Schema::hasColumn('employer_highlights', 'employer_id'));
        $this->assertTrue(Schema::hasColumn('employer_highlights', 'content'));
        $this->assertTrue(Schema::hasColumn('employer_highlights', 'created_at'));
        $this->assertTrue(Schema::hasColumn('employer_highlights', 'updated_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $highlight = EmployerHighlight::factory()
            ->for(Employer::factory(), 'employer')
            ->create();

        $this->assertInstanceOf(EmployerHighlight::class, $highlight);
        $this->assertInstanceOf(Employer::class, $highlight->employer);
    }
}

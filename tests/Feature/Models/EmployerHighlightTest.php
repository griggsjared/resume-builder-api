<?php

namespace Tests\Feature\Models;

use App\Models\Employer;
use App\Models\EmployerHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class EmployerHighlightTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('employer_highlights', [
                'id',
                'uuid',
                'employer_id',
                'content',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $highlight = EmployerHighlight::factory()->create();

        $this->assertTrue(
            Uuid::isValid($highlight->uuid)
        );
    }

    /** @test */
    public function has_relationships()
    {
        $highlight = EmployerHighlight::factory()
            ->for(Employer::factory(), 'employer')
            ->create();

        $this->assertInstanceOf(EmployerHighlight::class, $highlight);
        $this->assertInstanceOf(Employer::class, $highlight->employer);
    }
}

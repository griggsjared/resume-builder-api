<?php

namespace Tests\Feature\Models;

use App\Models\Employer;
use App\Models\EmployerHighlight;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class EmployerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('employers', [
                'id',
                'uuid',
                'subject_id',
                'name',
                'city',
                'state',
                'created_at',
                'updated_at',
                'started_at',
                'ended_at',
            ])
        );
    }

    /** @test */
    public function uuid_is_valid()
    {
        $employer = Employer::factory()->create();

        $this->assertTrue(
            Uuid::isValid($employer->uuid)
        );
    }

    /** @test */
    public function has_relationships()
    {
        $employer = Employer::factory()
            ->for(Subject::factory(), 'subject')
            ->has(EmployerHighlight::factory()->count(3), 'highlights')
            ->create();

        $this->assertInstanceOf(Employer::class, $employer);
        $this->assertInstanceOf(Subject::class, $employer->subject);
        $this->assertCount(3, $employer->highlights);
    }

    /** @test */
    public function can_be_current_or_past()
    {
        $current = Employer::factory()->current()->create();
        $past = Employer::factory()->create();

        $this->assertTrue($current->is_current);
        $this->assertFalse($past->is_current);
    }
}

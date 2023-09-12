<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmployerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('employers', 'id'));
        $this->assertTrue(Schema::hasColumn('employers', 'subject_id'));
        $this->assertTrue(Schema::hasColumn('employers', 'name'));
        $this->assertTrue(Schema::hasColumn('employers', 'city'));
        $this->assertTrue(Schema::hasColumn('employers', 'state'));
        $this->assertTrue(Schema::hasColumn('employers', 'created_at'));
        $this->assertTrue(Schema::hasColumn('employers', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('employers', 'started_at'));
        $this->assertTrue(Schema::hasColumn('employers', 'ended_at'));
    }

    /** @test */
    public function it_has_model_relationships()
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
    public function it_can_assess_an_is_current_attribute()
    {
        $current = Employer::factory()->current()->create();
        $past = Employer::factory()->create();

        $this->assertTrue($current->is_current);
        $this->assertFalse($past->is_current);
    }
}

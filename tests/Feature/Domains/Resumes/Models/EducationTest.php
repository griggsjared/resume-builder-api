<?php

namespace Tests\Feature\Domains\Resumes\Models;

use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function its_table_has_the_expected_columns()
    {
        $this->assertTrue(Schema::hasColumn('education', 'id'));
        $this->assertTrue(Schema::hasColumn('education', 'subject_id'));
        $this->assertTrue(Schema::hasColumn('education', 'name'));
        $this->assertTrue(Schema::hasColumn('education', 'city'));
        $this->assertTrue(Schema::hasColumn('education', 'state'));
        $this->assertTrue(Schema::hasColumn('education', 'major_degree'));
        $this->assertTrue(Schema::hasColumn('education', 'earned_major_degree'));
        $this->assertTrue(Schema::hasColumn('education', 'minor_degree'));
        $this->assertTrue(Schema::hasColumn('education', 'earned_minor_degree'));
        $this->assertTrue(Schema::hasColumn('education', 'created_at'));
        $this->assertTrue(Schema::hasColumn('education', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('education', 'started_at'));
        $this->assertTrue(Schema::hasColumn('education', 'ended_at'));
    }

    /** @test */
    public function it_has_model_relationships()
    {
        $education = Education::factory()
            ->for(Subject::factory(), 'subject')
            ->has(EducationHighlight::factory()->count(3), 'highlights')
            ->create();

        $this->assertInstanceOf(Education::class, $education);
        $this->assertInstanceOf(Subject::class, $education->subject);
        $this->assertCount(3, $education->highlights);
    }

    /** @test */
    public function it_can_assess_an_is_current_attribute()
    {
        $current = Education::factory()->current()->create();
        $past = Education::factory()->create();

        $this->assertTrue($current->is_current);
        $this->assertFalse($past->is_current);
    }

    /** @test */
    public function it_can_assess_an_earned_major_degree_attribute()
    {
        $earned = Education::factory()->create([
            'major_degree' => 'Bachelor of Science',
            'earned_major_degree' => true,
        ]);

        $notEarned = Education::factory()->create([
            'major_degree' => null,
            'earned_major_degree' => true,
        ]);

        $this->assertTrue($earned->earned_major_degree);
        $this->assertFalse($notEarned->earned_major_degree);
    }

    /** @test */
    public function it_can_assess_an_earned_minor_degree_attribute()
    {
        $earned = Education::factory()->create([
            'minor_degree' => 'Bachelor of Science',
            'earned_minor_degree' => true,
        ]);

        $notEarned = Education::factory()->create([
            'minor_degree' => null,
            'earned_minor_degree' => true,
        ]);

        $this->assertTrue($earned->earned_minor_degree);
        $this->assertFalse($notEarned->earned_minor_degree);
    }
}

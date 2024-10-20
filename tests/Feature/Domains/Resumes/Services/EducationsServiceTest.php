<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\EducationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationsServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_education(): void
    {
        $subject = Subject::factory()->create();

        $data = app(EducationsService::class)->upsert(
            EducationData::from([
                'name' => 'Acme',
                'city' => 'New York',
                'state' => 'NY',
                'major_degree' => 'B.S.',
                'earned_major_degree' => true,
                'minor_degree' => 'B.A.',
                'earned_minor_degree' => true,
                'started_at' => now()->subDay(),
                'ended_at' => now(),
                'subject' => $subject,
            ])
        );

        $education = Education::find($data->id);

        $this->assertInstanceOf(Subject::class, $education->subject);
        $this->assertEquals($subject->id, $education->subject->id);
        $this->assertEquals($data->name, $education->name);
        $this->assertEquals($data->city, $education->city);
        $this->assertEquals($data->state, $education->state);
        $this->assertEquals($data->major_degree, $education->major_degree);
        $this->assertEquals($data->earned_major_degree, $education->earned_major_degree);
        $this->assertEquals($data->minor_degree, $education->minor_degree);
        $this->assertEquals($data->earned_minor_degree, $education->earned_minor_degree);
        $this->assertEquals($data->started_at, $education->started_at);
        $this->assertEquals($data->ended_at, $education->ended_at);
    }

    /** @test */
    public function it_can_update_an_education()
    {
        $education = Education::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $data = app(EducationsService::class)->upsert(
            EducationData::from([
                ...$education->toArray(),
                'name' => 'Acme',
                'city' => 'New York',
                'state' => 'NY',
                'major_degree' => 'B.S.',
                'earned_major_degree' => true,
                'minor_degree' => 'B.A.',
                'earned_minor_degree' => true,
                'started_at' => now()->subDay(),
                'ended_at' => now(),
            ])
        );

        $education->refresh();

        $this->assertEquals($data->name, $education->name);
        $this->assertEquals($data->city, $education->city);
        $this->assertEquals($data->state, $education->state);
        $this->assertEquals($data->major_degree, $education->major_degree);
        $this->assertEquals($data->earned_major_degree, $education->earned_major_degree);
        $this->assertEquals($data->minor_degree, $education->minor_degree);
        $this->assertEquals($data->earned_minor_degree, $education->earned_minor_degree);
        $this->assertEquals($data->started_at, $education->started_at);
        $this->assertEquals($data->ended_at, $education->ended_at);
    }

    /** @test */
    public function it_can_update_a_education_with_a_subject()
    {
        $education = Education::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $subject = Subject::factory()->create();

        app(EducationsService::class)->upsert(
            EducationData::from([
                ...$education->toArray(),
                'subject' => $subject,
            ])
        );

        $education->refresh();

        $this->assertInstanceOf(Subject::class, $education->subject);
        $this->assertEquals($subject->id, $education->subject->id);
    }

    /** @test */
    public function it_can_upsert_education_highlights_for_an_education()
    {
        $education = Education::factory()
            ->has(Subject::factory(), 'subject')
            ->has(EducationHighlight::factory()->count(2), 'highlights')
            ->create();

        $keepHighlight = $education->highlights->first();
        $deleteHighlight = $education->highlights->last();

        app(EducationsService::class)->upsert(
            EducationData::from([
                ...$education->toArray(),
                'highlights' => [
                    [
                        ...$keepHighlight->toArray(),
                        'content' => 'I did a thing',
                    ],
                    [
                        'content' => 'I did another thing',
                    ],
                ],
            ])
        );

        $education->refresh();
        $keepHighlight->refresh();
        $deleteHighlight = EducationHighlight::find($deleteHighlight->id);

        $this->assertCount(2, $education->highlights);
        $this->assertEquals('I did a thing', $keepHighlight->content);
        $this->assertEquals($keepHighlight->id, $education->highlights->first()->id);
        $this->assertEquals('I did another thing', $education->highlights->last()->content);
        $this->assertNull($deleteHighlight);
    }

    /** @test */
    public function it_can_delete_an_education(): void
    {
        $education = Education::factory()
            ->has(EducationHighlight::factory(1), 'highlights')
            ->create();

        $educationHighlight = $education->highlights->first();

        app(EducationsService::class)->delete(EducationData::from($education));

        $education = Education::find($education->id);
        $educationHighlight = EducationHighlight::find($educationHighlight->id);

        $this->assertNull($education);
        $this->assertNull($educationHighlight);
    }

    /** @test */
    public function it_can_create_a_education_highlight(): void
    {
        $education = Education::factory()->create();

        $data = app(EducationsService::class)->upsertHighlight(
            EducationHighlightData::from([
                'content' => 'I am a highlight',
                'sort' => 1,
                'education' => $education,
            ])
        );

        $educationHighlight = EducationHighlight::find($data->id);

        $this->assertInstanceOf(Education::class, $educationHighlight->education);
        $this->assertEquals($education->id, $educationHighlight->education->id);
        $this->assertEquals($data->content, $educationHighlight->content);
        $this->assertEquals($data->sort, $educationHighlight->sort);
    }

    /** @test */
    public function it_can_update_a_education_highlight()
    {
        $educationHighlight = EducationHighlight::factory()
            ->has(Education::factory(), 'education')
            ->create();

        $data = app(EducationsService::class)->upsertHighlight(
            EducationHighlightData::from([
                ...$educationHighlight->toArray(),
                'content' => 'I am a highlight',
                'sort' => 1,
            ])
        );

        $educationHighlight->refresh();

        $this->assertEquals($data->content, $educationHighlight->content);
    }

    /** @test */
    public function it_can_update_a_education_highlight_with_an_education()
    {
        $educationHighlight = EducationHighlight::factory()
            ->has(Education::factory(), 'education')
            ->create();

        $education = Education::factory()->create();

        app(EducationsService::class)->upsertHighlight(
            EducationHighlightData::from([
                ...$educationHighlight->toArray(),
                'education' => $education,
            ])
        );

        $educationHighlight->refresh();

        $this->assertInstanceOf(Education::class, $educationHighlight->education);
        $this->assertEquals($education->id, $educationHighlight->education->id);
    }

    /** @test */
    public function it_can_delete_an_education_highlight(): void
    {
        $educationHighlight = EducationHighlight::factory()->create();

        app(EducationsService::class)->deleteHighlight(EducationHighlightData::from($educationHighlight));

        $educationHighlight = EducationHighlight::find($educationHighlight->id);

        $this->assertNull($educationHighlight);
    }
}

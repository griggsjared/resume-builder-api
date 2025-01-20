<?php

namespace Tests\Feature\Domains\Resumes\Services;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\EmployersService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployersServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_employer(): void
    {
        $subject = Subject::factory()->create();

        $data = app(EmployersService::class)->upsert(
            EmployerData::from([
                'name' => 'Acme',
                'city' => 'New York',
                'state' => 'NY',
                'started_at' => now()->subDay(),
                'ended_at' => now(),
                'subject' => $subject,
            ])
        );

        $employer = Employer::find($data->id);

        $this->assertInstanceOf(Subject::class, $employer->subject);
        $this->assertEquals($subject->id, $employer->subject->id);
        $this->assertEquals($data->name, $employer->name);
        $this->assertEquals($data->city, $employer->city);
        $this->assertEquals($data->state, $employer->state);
        $this->assertEquals($data->started_at, $employer->started_at);
        $this->assertEquals($data->ended_at, $employer->ended_at);
    }

    /** @test */
    public function it_can_update_an_employer()
    {
        $employer = Employer::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $data = app(EmployersService::class)->upsert(
            EmployerData::from([
                ...$employer->toArray(),
                'name' => 'Acme',
                'city' => 'New York',
                'state' => 'NY',
                'started_at' => now()->subDay(),
                'ended_at' => now(),
            ])
        );

        $employer->refresh();

        $this->assertEquals($data->name, $employer->name);
        $this->assertEquals($data->city, $employer->city);
        $this->assertEquals($data->state, $employer->state);
        $this->assertEquals($data->started_at, $employer->started_at);
        $this->assertEquals($data->ended_at, $employer->ended_at);
    }

    /** @test */
    public function it_can_update_a_employer_with_a_subject()
    {
        $employer = Employer::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $subject = Subject::factory()->create();

        app(EmployersService::class)->upsert(
            EmployerData::from([
                ...$employer->toArray(),
                'subject' => $subject,
            ])
        );

        $employer->refresh();

        $this->assertInstanceOf(Subject::class, $employer->subject);
        $this->assertEquals($subject->id, $employer->subject->id);
    }

    /** @test */
    public function it_can_upsert_employer_highlights_for_an_employer()
    {
        $employer = Employer::factory()
            ->has(Subject::factory(), 'subject')
            ->has(EmployerHighlight::factory()->count(2), 'highlights')
            ->create();

        $keepHighlight = $employer->highlights->first();
        $deleteHighlight = $employer->highlights->last();

        app(EmployersService::class)->upsert(
            EmployerData::from([
                ...$employer->toArray(),
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

        $employer->refresh();
        $keepHighlight->refresh();
        $deleteHighlight = EmployerHighlight::find($deleteHighlight->id);

        $this->assertCount(2, $employer->highlights);
        $this->assertEquals('I did a thing', $keepHighlight->content);
        $this->assertEquals($keepHighlight->id, $employer->highlights->first()->id);
        $this->assertEquals('I did another thing', $employer->highlights->last()->content);
        $this->assertNull($deleteHighlight);
    }

    /** @test */
    public function it_can_delete_a_employer(): void
    {
        $employer = Employer::factory()
            ->has(EmployerHighlight::factory(1), 'highlights')
            ->create();

        $employerHighlight = $employer->highlights->first();

        app(EmployersService::class)->delete(EmployerData::from($employer));

        $employer = Employer::find($employer->id);
        $employerHighlight = EmployerHighlight::find($employerHighlight->id);

        $this->assertNull($employer);
        $this->assertNull($employerHighlight);
    }

    /** @test */
    public function it_can_create_a_employer_highlight(): void
    {
        $employer = Employer::factory()->create();

        $data = app(EmployersService::class)->upsertHighlight(
            EmployerHighlightData::from([
                'content' => 'I am a highlight',
                'sort' => 1,
                'employer' => $employer,
            ])
        );

        $employerHighlight = EmployerHighlight::find($data->id);

        $this->assertInstanceOf(Employer::class, $employerHighlight->employer);
        $this->assertEquals($employer->id, $employerHighlight->employer->id);
        $this->assertEquals($data->content, $employerHighlight->content);
        $this->assertEquals($data->sort, $employerHighlight->sort);
    }

    /** @test */
    public function it_can_update_a_employer_highlight()
    {
        $employerHighlight = EmployerHighlight::factory()
            ->has(Employer::factory(), 'employer')
            ->create();

        $data = app(EmployersService::class)->upsertHighlight(
            EmployerHighlightData::from([
                ...$employerHighlight->toArray(),
                'content' => 'I am a highlight',
                'sort' => 1,
            ])
        );

        $employerHighlight->refresh();

        $this->assertEquals($data->content, $employerHighlight->content);
        $this->assertEquals($data->sort, $employerHighlight->sort);
    }

    /** @test */
    public function it_can_update_a_employer_highlight_with_an_employer()
    {
        $employerHighlight = EmployerHighlight::factory()
            ->has(Employer::factory(), 'employer')
            ->create();

        $employer = Employer::factory()->create();

        app(EmployersService::class)->upsertHighlight(
            EmployerHighlightData::from([
                ...$employerHighlight->toArray(),
                'employer' => $employer,
            ])
        );

        $employerHighlight->refresh();

        $this->assertInstanceOf(Employer::class, $employerHighlight->employer);
        $this->assertEquals($employer->id, $employerHighlight->employer->id);
    }

    /** @test */
    public function it_can_delete_a_employer_highlight(): void
    {
        $employerHighlight = EmployerHighlight::factory()->create();

        app(EmployersService::class)->deleteHighlight(EmployerHighlightData::from($employerHighlight));

        $employerHighlight = EmployerHighlight::find($employerHighlight->id);

        $this->assertNull($employerHighlight);
    }
}

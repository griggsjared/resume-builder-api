<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\UpsertEmployerHighlightAction;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertEmployerHighlightActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_employer_highlight(): void
    {
        $employer = Employer::factory()->create();

        $data = app(UpsertEmployerHighlightAction::class)->execute(
            EmployerHighlightData::from([
                'content' => 'I am a highlight',
                'employer' => $employer,
            ])
        );

        $employerHighlight = EmployerHighlight::find($data->id);

        $this->assertInstanceOf(Employer::class, $employerHighlight->employer);
        $this->assertEquals($employer->id, $employerHighlight->employer->id);
        $this->assertEquals($data->content, $employerHighlight->content);
    }

    /** @test */
    public function it_can_update_a_employer_highlight()
    {
        $employerHighlight = EmployerHighlight::factory()
            ->has(Employer::factory(), 'employer')
            ->create();

        $data = app(UpsertEmployerHighlightAction::class)->execute(
            EmployerHighlightData::from([
                ...$employerHighlight->toArray(),
                'content' => 'I am a highlight',
            ])
        );

        $employerHighlight->refresh();

        $this->assertEquals($data->content, $employerHighlight->content);
    }

    /** @test */
    public function it_can_update_a_employer_highlight_with_an_employer()
    {
        $employerHighlight = EmployerHighlight::factory()
            ->has(Employer::factory(), 'employer')
            ->create();

        $employer = Employer::factory()->create();

        app(UpsertEmployerHighlightAction::class)->execute(
            EmployerHighlightData::from([
                ...$employerHighlight->toArray(),
                'employer' => $employer,
            ])
        );

        $employerHighlight->refresh();

        $this->assertInstanceOf(Employer::class, $employerHighlight->employer);
        $this->assertEquals($employer->id, $employerHighlight->employer->id);
    }
}

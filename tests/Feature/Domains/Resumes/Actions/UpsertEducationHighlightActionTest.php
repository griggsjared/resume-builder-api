<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\UpsertEducationHighlightAction;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertEducationHighlightActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_education_highlight(): void
    {
        $education = Education::factory()->create();

        $data = app(UpsertEducationHighlightAction::class)->execute(
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

        $data = app(UpsertEducationHighlightAction::class)->execute(
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

        app(UpsertEducationHighlightAction::class)->execute(
            EducationHighlightData::from([
                ...$educationHighlight->toArray(),
                'education' => $education,
            ])
        );

        $educationHighlight->refresh();

        $this->assertInstanceOf(Education::class, $educationHighlight->education);
        $this->assertEquals($education->id, $educationHighlight->education->id);
    }
}

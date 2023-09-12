<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteEducationHighlightAction;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\EducationHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteEducationHighlightActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_an_education(): void
    {
        $educationHighlight = EducationHighlight::factory()->create();

        app(DeleteEducationHighlightAction::class)->execute(EducationHighlightData::from($educationHighlight));

        $educationHighlight = EducationHighlight::find($educationHighlight->id);

        $this->assertNull($educationHighlight);
    }
}

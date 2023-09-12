<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteSubjectHighlightAction;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\SubjectHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteSubjectHighlightActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_subject(): void
    {
        $subjectHighlight = SubjectHighlight::factory()->create();

        app(DeleteSubjectHighlightAction::class)->execute(SubjectHighlightData::from($subjectHighlight));

        $subjectHighlight = SubjectHighlight::find($subjectHighlight->id);

        $this->assertNull($subjectHighlight);
    }
}

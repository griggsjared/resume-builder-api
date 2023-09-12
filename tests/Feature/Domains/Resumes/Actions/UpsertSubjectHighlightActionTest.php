<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\UpsertSubjectHighlightAction;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertSubjectHighlightActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_subject_highlight(): void
    {
        $subject = Subject::factory()->create();

        $data = app(UpsertSubjectHighlightAction::class)->execute(
            SubjectHighlightData::from([
                'content' => 'I am a highlight',
                'subject' => $subject,
            ])
        );

        $subjectHighlight = SubjectHighlight::find($data->id);

        $this->assertInstanceOf(Subject::class, $subjectHighlight->subject);
        $this->assertEquals($subject->id, $subjectHighlight->subject->id);
        $this->assertEquals($data->content, $subjectHighlight->content);
    }

    /** @test */
    public function it_can_update_a_subject_highlight()
    {
        $subjectHighlight = SubjectHighlight::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $data = app(UpsertSubjectHighlightAction::class)->execute(
            SubjectHighlightData::from([
                ...$subjectHighlight->toArray(),
                'content' => 'I am a highlight',
            ])
        );

        $subjectHighlight->refresh();

        $this->assertEquals($data->content, $subjectHighlight->content);
    }

    /** @test */
    public function it_can_update_a_subject_highlight_with_a_subject()
    {
        $subjectHighlight = SubjectHighlight::factory()
            ->has(Subject::factory(), 'subject')
            ->create();

        $subject = Subject::factory()->create();

        app(UpsertSubjectHighlightAction::class)->execute(
            SubjectHighlightData::from([
                ...$subjectHighlight->toArray(),
                'subject' => $subject,
            ])
        );

        $subjectHighlight->refresh();

        $this->assertInstanceOf(Subject::class, $subjectHighlight->subject);
        $this->assertEquals($subject->id, $subjectHighlight->subject->id);
    }
}

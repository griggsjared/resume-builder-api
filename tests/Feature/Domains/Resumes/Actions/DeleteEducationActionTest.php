<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteEducationAction;
use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteEducationActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_an_education(): void
    {
        $education = Education::factory()
            ->has(EducationHighlight::factory(1), 'highlights')
            ->create();

        $educationHighlight = $education->highlights->first();

        app(DeleteEducationAction::class)->execute(EducationData::from($education));

        $education = Education::find($education->id);
        $educationHighlight = EducationHighlight::find($educationHighlight->id);

        $this->assertNull($education);
        $this->assertNull($educationHighlight);
    }
}

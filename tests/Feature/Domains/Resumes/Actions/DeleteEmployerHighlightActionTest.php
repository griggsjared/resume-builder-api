<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteEmployerHighlightAction;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\EmployerHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteEmployerHighlightActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_employer(): void
    {
        $employerHighlight = EmployerHighlight::factory()->create();

        app(DeleteEmployerHighlightAction::class)->execute(EmployerHighlightData::from($employerHighlight));

        $employerHighlight = EmployerHighlight::find($employerHighlight->id);

        $this->assertNull($employerHighlight);
    }
}

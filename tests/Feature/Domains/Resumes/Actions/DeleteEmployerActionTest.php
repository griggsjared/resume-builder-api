<?php

namespace Tests\Feature\Domains\Resumes\Actions;

use App\Domains\Resumes\Actions\DeleteEmployerAction;
use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteEmployerActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_a_employer(): void
    {
        $employer = Employer::factory()
            ->has(EmployerHighlight::factory(1), 'highlights')
            ->create();

        $employerHighlight = $employer->highlights->first();

        app(DeleteEmployerAction::class)->execute(EmployerData::from($employer));

        $employer = Employer::find($employer->id);
        $employerHighlight = EmployerHighlight::find($employerHighlight->id);

        $this->assertNull($employer);
        $this->assertNull($employerHighlight);
    }
}

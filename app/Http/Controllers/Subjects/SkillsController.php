<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Actions\DeleteSkillAction;
use App\Domains\Resumes\Actions\UpsertSkillAction;
use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertSkillRequest;
use App\Http\ViewData\PaginatedViewData;
use App\Http\ViewData\SkillViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    public function __construct(
        private UpsertSkillAction $upsertSkillAction,
        private DeleteSkillAction $deleteSkillAction,
    ) {
    }

    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $skills = $subject->skills();

        if($request->has('search')) {
            $skills->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match($request->input('order_by')) {
            'name' => $skills->orderBy('name', $order),
            'category' => $skills->orderBy('category', $order),
            default => $skills->orderBy('name', $order),
        };

        /**
         * @var PaginatedViewData<SkillViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $skills->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            SkillViewData::class
        );

        return response()->json($viewData);
    }

    public function store(UpsertSkillRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->upsertSkillAction->execute(
            SkillData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(SkillViewData::from(
            Skill::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Skill $skill): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(SkillViewData::from($skill));
    }

    public function update(UpsertSkillRequest $request, Subject $subject, Skill $skill): JsonResponse
    {
        $this->upsertSkillAction->execute(
            SkillData::from([
                ...$skill->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(SkillViewData::from(
            $skill->refresh()
        ));
    }

    public function destroy(Subject $subject, Skill $skill): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->deleteSkillAction->execute(
            SkillData::from($skill)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

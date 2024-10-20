<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Data\SkillData;
use App\Domains\Resumes\Models\Skill;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\SkillsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertSkillRequest;
use App\Http\ApiData\PaginatedApiData;
use App\Http\ApiData\SkillApiData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    public function __construct(
        private SkillsService  $skillsService,
    ) {}

    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $skills = $subject->skills();

        if ($request->has('search')) {
            $skills->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'name' => $skills->orderBy('name', $order),
            'category' => $skills->orderBy('category', $order),
            'sort' => $skills->orderBy('sort', $order),
            default => $skills->orderBy('sort', $order),
        };

        /**
         * @var PaginatedApiData<SkillApiData>
         */
        $ApiData = PaginatedApiData::fromPaginator(
            $skills->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            SkillApiData::class
        );

        return response()->json($ApiData);
    }

    public function store(UpsertSkillRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->skillsService->upsert(
            SkillData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(SkillApiData::from(
            Skill::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Skill $skill): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(SkillApiData::from($skill));
    }

    public function update(UpsertSkillRequest $request, Subject $subject, Skill $skill): JsonResponse
    {
        $this->skillsService->upsert(
            SkillData::from([
                ...$skill->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(SkillApiData::from(
            $skill->refresh()
        ));
    }

    public function destroy(Subject $subject, Skill $skill): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->skillsService->delete(
            SkillData::from($skill)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Actions\DeleteSubjectAction;
use App\Domains\Resumes\Actions\UpsertSubjectAction;
use App\Domains\Resumes\Data\SubjectData;
use App\Domains\Resumes\Models\Subject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\StoreSubjectRequest;
use App\Http\Requests\Subjects\UpdateSubjectRequest;
use App\Http\ViewData\PaginatedViewData;
use App\Http\ViewData\SubjectViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function __construct(
        private UpsertSubjectAction $upsertSubjectAction,
        private DeleteSubjectAction $deleteSubjectAction,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Subject::class);

        /**
         * @var PaginatedViewData<SubjectViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            Subject::authorized($request->user())->orderBy('created_at', 'asc')->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            SubjectViewData::class
        );

        return response()->json($viewData);
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $data = $this->upsertSubjectAction->execute(
            SubjectData::from([
                ...$request->validated(),
                'user' => $request->assignUser(),
            ])
        );

        return response()->json(SubjectViewData::from($data), 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(SubjectViewData::from($subject));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->upsertSubjectAction->execute(
            SubjectData::from([
                ...$subject->toArray(),
                ...$request->validated(),
                'user' => $request->assignUser(),
            ])
        );

        return response()->json(SubjectViewData::from($data));
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $this->authorize('delete', $subject);

        $this->deleteSubjectAction->execute(
            SubjectData::from($subject->toArray())
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

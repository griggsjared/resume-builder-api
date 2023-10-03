<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Actions\DeleteSubjectHighlightAction;
use App\Domains\Resumes\Actions\UpsertSubjectHighlightAction;
use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertSubjectHighlightRequest;
use App\Http\ViewData\PaginatedViewData;
use App\Http\ViewData\SubjectHighlightViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectHighlightsController extends Controller
{
    public function __construct(
        private UpsertSubjectHighlightAction $upsertSubjectHighlightAction,
        private DeleteSubjectHighlightAction $deleteSubjectHighlightAction,
    ) {
    }

    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        /**
         * @var PaginatedViewData<SubjectHighlightViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $subject->highlights()->orderBy('created_at', 'asc')->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            SubjectHighlightViewData::class
        );

        return response()->json($viewData);
    }

    public function store(UpsertSubjectHighlightRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->upsertSubjectHighlightAction->execute(
            SubjectHighlightData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(SubjectHighlightViewData::from($data), 201);
    }

    public function show(Subject $subject, SubjectHighlight $highlight): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(SubjectHighlightViewData::from($highlight));
    }

    public function update(UpsertSubjectHighlightRequest $request, Subject $subject, SubjectHighlight $highlight): JsonResponse
    {
        $data = $this->upsertSubjectHighlightAction->execute(
            SubjectHighlightData::from([
                ...$highlight->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(SubjectHighlightViewData::from($data));
    }

    public function destroy(Subject $subject, SubjectHighlight $highlight): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->deleteSubjectHighlightAction->execute(
            SubjectHighlightData::from($highlight)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Actions\DeleteEducationHighlightAction;
use App\Domains\Resumes\Actions\UpsertEducationHighlightAction;
use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertEducationHighlightRequest;
use App\Http\ViewData\EducationHighlightViewData;
use App\Http\ViewData\PaginatedViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationHighlightsController extends Controller
{
    public function __construct(
        private UpsertEducationHighlightAction $upsertEducationHighlightAction,
        private DeleteEducationHighlightAction $deleteEducationHighlightAction,
    ) {
    }

    public function index(Request $request, Subject $subject, Education $education): JsonResponse
    {
        $this->authorize('view', $subject);

        /**
         * @var PaginatedViewData<EducationHighlightViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $education->highlights()->orderBy('created_at', 'asc')->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            EducationHighlightViewData::class
        );

        return response()->json($viewData);
    }

    public function store(UpsertEducationHighlightRequest $request, Subject $subject, Education $education): JsonResponse
    {
        $data = $this->upsertEducationHighlightAction->execute(
            EducationHighlightData::from([
                ...$request->validated(),
                'education' => $education
            ])
        );

        return response()->json(EducationHighlightViewData::from($data), 201);
    }

    public function show(Subject $subject, Education $education, EducationHighlight $highlight): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(EducationHighlightViewData::from($highlight));
    }

    public function update(UpsertEducationHighlightRequest $request, Subject $subject, Education $education, EducationHighlight $highlight): JsonResponse
    {
        $data = $this->upsertEducationHighlightAction->execute(
            EducationHighlightData::from([
                ...$highlight->toArray(),
                ...$request->validated()
            ])
        );

        return response()->json(EducationHighlightViewData::from($data));
    }

    public function destroy(Subject $subject, Education $education, EducationHighlight $highlight): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->deleteEducationHighlightAction->execute(
            EducationHighlightData::from($highlight)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Data\SubjectHighlightData;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Models\SubjectHighlight;
use App\Domains\Resumes\Services\SubjectsService;
use App\Http\ApiData\PaginatedApiData;
use App\Http\ApiData\SubjectHighlightApiData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertSubjectHighlightRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectHighlightsController extends Controller
{
    public function __construct(
        private SubjectsService $subjectsService,
    ) {}

    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $highlights = $subject->highlights();

        if ($request->has('search')) {
            $highlights->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'sort' => $highlights->orderBy('sort', $order),
            default => $highlights->orderBy('sort', $order),
        };

        /**
         * @var PaginatedApiData<SubjectHighlightApiData>
         */
        $ApiData = PaginatedApiData::fromPaginator(
            $highlights->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            SubjectHighlightApiData::class
        );

        return response()->json($ApiData);
    }

    public function store(UpsertSubjectHighlightRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->subjectsService->upsertHighlight(
            SubjectHighlightData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(SubjectHighlightApiData::from(
            SubjectHighlight::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, SubjectHighlight $highlight): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(SubjectHighlightApiData::from($highlight));
    }

    public function update(UpsertSubjectHighlightRequest $request, Subject $subject, SubjectHighlight $highlight): JsonResponse
    {
        $data = $this->subjectsService->upsertHighlight(
            SubjectHighlightData::from([
                ...$highlight->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(SubjectHighlightApiData::from(
            $highlight->refresh()
        ));
    }

    public function destroy(Subject $subject, SubjectHighlight $highlight): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->subjectsService->deleteHighlight(
            SubjectHighlightData::from($highlight)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

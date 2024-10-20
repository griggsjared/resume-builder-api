<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Data\EducationHighlightData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\EducationHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\EducationsService;
use App\Http\ApiData\EducationHighlightApiData;
use App\Http\ApiData\PaginatedApiData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertEducationHighlightRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationHighlightsController extends Controller
{
    public function __construct(
        private EducationsService $educationsService,
    ) {}

    public function index(Request $request, Subject $subject, Education $education): JsonResponse
    {
        $this->authorize('view', $subject);

        $highlights = $education->highlights();

        if ($request->has('search')) {
            $highlights->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'sort' => $highlights->orderBy('sort', $order),
            default => $highlights->orderBy('sort', $order),
        };

        /**
         * @var PaginatedApiData<EducationHighlightApiData>
         */
        $ApiData = PaginatedApiData::fromPaginator(
            $highlights->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            EducationHighlightApiData::class
        );

        return response()->json($ApiData);
    }

    public function store(UpsertEducationHighlightRequest $request, Subject $subject, Education $education): JsonResponse
    {
        $data = $this->educationsService->upsertHighlight(
            EducationHighlightData::from([
                ...$request->validated(),
                'education' => $education,
            ])
        );

        return response()->json(EducationHighlightApiData::from(
            EducationHighlight::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Education $education, EducationHighlight $highlight): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(EducationHighlightApiData::from($highlight));
    }

    public function update(UpsertEducationHighlightRequest $request, Subject $subject, Education $education, EducationHighlight $highlight): JsonResponse
    {
        $data = $this->educationsService->upsertHighlight(
            EducationHighlightData::from([
                ...$highlight->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(EducationHighlightApiData::from(
            $highlight->refresh()
        ));
    }

    public function destroy(Subject $subject, Education $education, EducationHighlight $highlight): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->educationsService->deleteHighlight(
            EducationHighlightData::from($highlight)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Data\EducationData;
use App\Domains\Resumes\Models\Education;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\EducationsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertEducationRequest;
use App\Http\ViewData\EducationViewData;
use App\Http\ViewData\PaginatedViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationsController extends Controller
{
    public function __construct(
        private EducationsService $educationsService,
    ) {}

    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $education = $subject->education();

        if ($request->has('search')) {
            $education->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'name' => $education->orderBy('name', $order),
            'city' => $education->orderBy('city', $order),
            'state' => $education->orderBy('state', $order),
            'major_degree' => $education->orderBy('major_degree', $order),
            'minor_degree' => $education->orderBy('minor_degree', $order),
            'started_at' => $education->orderBy('started_at', $order),
            'ended_at' => $education->orderBy('ended_at', $order),
            default => $education->orderBy('name', $order),
        };

        /**
         * @var PaginatedViewData<EducationViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $education->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            EducationViewData::class
        );

        return response()->json($viewData);
    }

    public function store(UpsertEducationRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->educationsService->upsert(
            EducationData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(EducationViewData::from(
            Education::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Education $education): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(EducationViewData::from($education));
    }

    public function update(UpsertEducationRequest $request, Subject $subject, Education $education): JsonResponse
    {
        $this->educationsService->upsert(
            EducationData::from([
                ...$education->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(EducationViewData::from(
            $education->refresh()
        ));
    }

    public function destroy(Subject $subject, Education $education): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->educationsService->delete(
            EducationData::from($education)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

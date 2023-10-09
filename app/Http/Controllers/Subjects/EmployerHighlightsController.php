<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Actions\DeleteEmployerHighlightAction;
use App\Domains\Resumes\Actions\UpsertEmployerHighlightAction;
use App\Domains\Resumes\Data\EmployerHighlightData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\EmployerHighlight;
use App\Domains\Resumes\Models\Subject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertEmployerHighlightRequest;
use App\Http\ViewData\EmployerHighlightViewData;
use App\Http\ViewData\PaginatedViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployerHighlightsController extends Controller
{
    public function __construct(
        private UpsertEmployerHighlightAction $upsertEmployerHighlightAction,
        private DeleteEmployerHighlightAction $deleteEmployerHighlightAction,
    ) {
    }

    public function index(Request $request, Subject $subject, Employer $employer): JsonResponse
    {
        $this->authorize('view', $subject);

        $highlights = $employer->highlights();

        if ($request->has('search')) {
            $highlights->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'sort' => $highlights->orderBy('sort', $order),
            default => $highlights->orderBy('sort', $order),
        };

        /**
         * @var PaginatedViewData<EmployerHighlightViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $highlights->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            EmployerHighlightViewData::class
        );

        return response()->json($viewData);
    }

    public function store(UpsertEmployerHighlightRequest $request, Subject $subject, Employer $employer): JsonResponse
    {
        $data = $this->upsertEmployerHighlightAction->execute(
            EmployerHighlightData::from([
                ...$request->validated(),
                'employer' => $employer,
            ])
        );

        return response()->json(EmployerHighlightViewData::from(
            EmployerHighlight::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Employer $employer, EmployerHighlight $highlight): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(EmployerHighlightViewData::from($highlight));
    }

    public function update(UpsertEmployerHighlightRequest $request, Subject $subject, Employer $employer, EmployerHighlight $highlight): JsonResponse
    {
        $data = $this->upsertEmployerHighlightAction->execute(
            EmployerHighlightData::from([
                ...$highlight->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(EmployerHighlightViewData::from(
            $highlight->refresh()
        ));
    }

    public function destroy(Subject $subject, Employer $employer, EmployerHighlight $highlight): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->deleteEmployerHighlightAction->execute(
            EmployerHighlightData::from($highlight)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

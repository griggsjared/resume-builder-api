<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Actions\DeleteEmployerAction;
use App\Domains\Resumes\Actions\UpsertEmployerAction;
use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Subject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertEmployerRequest;
use App\Http\ViewData\EmployerViewData;
use App\Http\ViewData\PaginatedViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployersController extends Controller
{
    public function __construct(
        private UpsertEmployerAction $upsertEmployerAction,
        private DeleteEmployerAction $deleteEmployerAction,
    ) {}

    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $employers = $subject->employers();

        if ($request->has('search')) {
            $employers->search($request->input('search'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'name' => $employers->orderBy('name', $order),
            'city' => $employers->orderBy('city', $order),
            'state' => $employers->orderBy('state', $order),
            'started_at' => $employers->orderBy('started_at', $order),
            'ended_at' => $employers->orderBy('ended_at', $order),
            default => $employers->orderBy('name', $order),
        };

        /**
         * @var PaginatedViewData<EmployerViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $employers->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            EmployerViewData::class
        );

        return response()->json($viewData);
    }

    public function store(UpsertEmployerRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->upsertEmployerAction->execute(
            EmployerData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(EmployerViewData::from(
            Employer::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Employer $employer): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(EmployerViewData::from($employer));
    }

    public function update(UpsertEmployerRequest $request, Subject $subject, Employer $employer): JsonResponse
    {
        $this->upsertEmployerAction->execute(
            EmployerData::from([
                ...$employer->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(EmployerViewData::from(
            $employer->refresh()
        ));
    }

    public function destroy(Subject $subject, Employer $employer): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->deleteEmployerAction->execute(
            EmployerData::from($employer)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

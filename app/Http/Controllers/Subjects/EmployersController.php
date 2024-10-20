<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subjects;

use App\Domains\Resumes\Data\EmployerData;
use App\Domains\Resumes\Models\Employer;
use App\Domains\Resumes\Models\Subject;
use App\Domains\Resumes\Services\EmployersService;
use App\Http\ApiData\EmployerApiData;
use App\Http\ApiData\PaginatedApiData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subjects\UpsertEmployerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployersController extends Controller
{
    public function __construct(
        private EmployersService $employersService,
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
         * @var PaginatedApiData<EmployerApiData>
         */
        $ApiData = PaginatedApiData::fromPaginator(
            $employers->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            EmployerApiData::class
        );

        return response()->json($ApiData);
    }

    public function store(UpsertEmployerRequest $request, Subject $subject): JsonResponse
    {
        $data = $this->employersService->upsert(
            EmployerData::from([
                ...$request->validated(),
                'subject' => $subject,
            ])
        );

        return response()->json(EmployerApiData::from(
            Employer::find($data->id)
        ), 201);
    }

    public function show(Subject $subject, Employer $employer): JsonResponse
    {
        $this->authorize('view', $subject);

        return response()->json(EmployerApiData::from($employer));
    }

    public function update(UpsertEmployerRequest $request, Subject $subject, Employer $employer): JsonResponse
    {
        $this->employersService->upsert(
            EmployerData::from([
                ...$employer->toArray(),
                ...$request->validated(),
            ])
        );

        return response()->json(EmployerApiData::from(
            $employer->refresh()
        ));
    }

    public function destroy(Subject $subject, Employer $employer): JsonResponse
    {
        $this->authorize('update', $subject);

        $this->employersService->delete(
            EmployerData::from($employer)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

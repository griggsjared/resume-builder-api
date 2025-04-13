<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Models\User;
use App\Domains\Users\Services\UsersService;
use App\Http\ApiData\PaginatedApiData;
use App\Http\ApiData\UserApiData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct(
        private UsersService $usersService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::authorized($request->user());

        if ($request->has('search')) {
            $users->search($request->input('search'));
        }

        if ($request->has('role') && UserRole::isValid($request->input('role'))) {
            $users->where('role', $request->input('role'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'email' => $users->orderBy('email', $order),
            'role' => $users->orderBy('role', $order),
            'updated_at' => $users->orderBy('updated_at', $order),
            default => $users->orderBy('created_at', $order),
        };

        /**
         * @var PaginatedApiData<UserApiData>
         */
        $ApiData = PaginatedApiData::fromPaginator(
            $users->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            UserApiData::class
        );

        return response()->json($ApiData);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $this->usersService->upsert(
            $request->userData()
        );

        return response()->json(UserApiData::from(
            User::find($data->id)
        ), 201);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json(
            UserApiData::from($user)
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->usersService->upsert(
            $request->userData()
        );

        return response()->json(UserApiData::from(
            $user->refresh()
        ));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->usersService->delete(
            UserData::from($user)
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Actions\UpsertUserAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\ViewData\PaginatedViewData;
use App\Http\ViewData\UserViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct(
        private UpsertUserAction $upsertUserAction,
        private DeleteUserAction $deleteUserAction,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        /**
         * @var PaginatedViewData<UserViewData> $viewData
         */
        $viewData = PaginatedViewData::fromPaginator(
            User::authorized($request->user())->orderBy('created_at', 'asc')->paginate(
                $request->input('per_page', 20)
            )->withQueryString(),
            UserViewData::class
        );

        return response()->json($viewData);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $this->upsertUserAction->execute(
            UserData::from([
                ...$request->validated(),
                'role' => $request->assignRole(),
            ])
        );

        return response()->json(
            UserViewData::from($data), 201
        );
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json(
            UserViewData::from($user)
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $this->upsertUserAction->execute(
            UserData::from([
                ...$user->toArray(),
                ...$request->validated(),
                'role' => $request->assignRole()
            ])
        );

        return response()->json(
            UserViewData::from($data)
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->deleteUserAction->execute(
            UserData::from($user->toArray())
        );

        return response()->json([
            'message' => 'Ok',
        ]);
    }
}

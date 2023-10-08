<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Actions\UpsertUserAction;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Enums\UserRole;
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

        $users = User::authorized($request->user());

        if ($request->has('search')) {
            $users->search($request->input('search'));
        }

        if ($request->has('role') && UserRole::isValid($request->input('role'))) {
            $users->role($request->input('role'));
        }

        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        match ($request->input('order_by')) {
            'email' => $users->orderBy('email', $order),
            'role' => $users->orderBy('role', $order),
            'updated_at' => $users->orderBy('updated_at', $order),
            default => $users->orderBy('created_at', $order),
        };

        /**
         * @var PaginatedViewData<UserViewData>
         */
        $viewData = PaginatedViewData::fromPaginator(
            $users->paginate(
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

        return response()->json(UserViewData::from(
            User::find($data->id)
        ), 201);
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
        $this->upsertUserAction->execute(
            UserData::from([
                ...$user->toArray(),
                ...$request->validated(),
                'role' => $request->assignRole(),
            ])
        );

        return response()->json(UserViewData::from(
            $user->refresh()
        ));
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

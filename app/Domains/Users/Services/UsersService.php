<?php

declare(strict_types=1);

namespace App\Domains\Users\Services;

use App\Domains\Users\Data\AccessTokenData;
use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\AccessToken;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\LaravelData\Optional;

class UsersService
{
    public function __construct(
        private AccessTokensService $accessTokensService,
    ) {}

    public function upsert(UserData $data): UserData
    {
        $user = User::updateOrCreate(
            ['id' => $data->id],
            [
                'email' => $data->email,
            ]
        );

        $user->role = $data->role;

        if (! $data->password instanceof Optional) {
            $user->password = Hash::make($data->password);
        }

        $user->save();

        return UserData::from($user);
    }

    public function delete(UserData $user): ?UserData
    {
        $user = User::find($user->id);

        if (! $user) {
            return null;
        }

        $user->accessTokens->each(function (AccessToken $token) {
            $this->accessTokensService->delete(
                AccessTokenData::from($token)
            );
        });

        $user->delete();

        return UserData::from($user);
    }
}

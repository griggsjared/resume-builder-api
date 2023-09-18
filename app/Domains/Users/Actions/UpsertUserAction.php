<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\LaravelData\Optional;

class UpsertUserAction
{
    public function execute(UserData $data): UserData
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
}

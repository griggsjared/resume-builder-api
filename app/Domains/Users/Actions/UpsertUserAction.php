<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Data\UserData;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;

class UpsertUserAction
{
    public function handle(UserData $data): UserData
    {
        $user = User::updateOrCreate(
            ['id' => $data->id],
            [
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
            ]
        );

        $user->role = $data->role;
        if ($data->password || $user->wasRecentlyCreated) {
            $user->password = Hash::make($data->password ?? 'Secret!123');
        }

        $user->save();

        return UserData::from($user);
    }
}

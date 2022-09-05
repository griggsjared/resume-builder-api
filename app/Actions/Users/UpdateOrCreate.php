<?php

namespace App\Actions\Users;

use App\DTO\UserData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateOrCreate
{
    public function execute(UserData $data): User
    {
        $user = User::where('id', $data->id)->first();

        if (! $user) {
            $user = User::create([
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'password' => Hash::make($data->password ?? 'Secret!'),
                'role' => $data->role,
            ]);
        }

        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->password = Hash::make($data->password ?? 'Secret!');
        $user->role = $data->role;
        $user->save();

        return $user;
    }
}

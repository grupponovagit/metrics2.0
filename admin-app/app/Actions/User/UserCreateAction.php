<?php

namespace App\Actions\User;

use App\Models\User;
use App\Data\User\UserCreateData;

class UserCreateAction
{
    public function handle(UserCreateData $data): User
    {
        $user = User::create([
            'name' => $data->getName(),
            'surname' => $data->getSurname(),
            'email' => $data->getEmail(),
            'password' => $data->getHashPassword(),
        ]);

        $user->assignRole($data->getRoles());

        return $user;
    }
}



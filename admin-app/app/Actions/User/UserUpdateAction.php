<?php

namespace App\Actions\User;

use App\Models\User;
use App\Data\User\UserUpdateData;

class UserUpdateAction
{
    public function handle(UserUpdateData $data, User $user): User
    {
        $user->update([
            'name' => $data->getName(),
            'surname' => $data->getSurname(),
            'email' => $data->getEmail(),
        ]);

        if ($data->password) {
            $user->update([
                'password' => $data->getHashPassword(),
            ]);
        }

        $user->syncRoles($data->getRoles());

        return $user;
    }
}




<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserService
{

    /**
     * get all users.
     *
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\User[]
     */
    public function getAllUsers() {
        return User::all();
    }

    /**
     * Get user by id.
     *
     * @param $data
     * @return \App\Models\User
     */
    public function showUser($data) {
        $result = User::findOrFail($data["user_id"]);
        return $result;
    }


    /**
     * Create new user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function saveUser(array $data)
    {
        $user = DB::transaction(function() use ($data){
            $result = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'document' => $data['document'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'address' => $data['address'],
                'date_of_birth' => $data['date_of_birth'],
                'country_id' => $data['country_id'],
            ]);

            if(isset($data['role'])) {
                $role = Role::findByName($data['role']);
            } else {
                $role = Role::findByName('student');
            }
            $result->assignRole($role);
            return $result;
        });

        return $user;
    }

    /**
     * Update user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function updateUser(array $data, $id) {

        $user = User::findOrFail($id);

        $result = DB::transaction(function() use ($user, $data){

            $updates = [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'phone_number' => $data['phone_number'] ?? $user->phone_number,
                'document' => $data['document'] ?? $user->document,
                'city' => $data['city'] ?? $user->city,
                'postal_code' => $data['postal_code'] ?? $user->postal_code,
                'address' => $data['address'] ?? $user->address,
                'date_of_birth' => $data['date_of_birth'] ?? $user->date_of_birth,
                'country_id' => $data['country_id'] ?? $user->country_id,
            ];

            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->update($updates);

            if (isset($data['role'])) {
                $role = Role::findByName($data['role']);
                $user->syncRoles([$role]);
            }
            return $user;

        });
        return $result;  
    }

    public function deleteUser($id)
    {
        DB::transaction(function() use ($id) {
            $user = User::findOrFail($id);
            $user->delete();
        });
    }
}
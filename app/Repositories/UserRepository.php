<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function register($data)
    {
        $data->validate([
            'name' => 'required|min:1',
            'email' => 'required|email',
            'password' => 'required|min:1',
        ]);

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password)
        ]);

        $token = $user->createToken('Laravel9PassportAuth')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login($data)
    {
        $userdata = [
            'name' => $data->name,
            'password' => $data->password
        ];

        if (Auth::attempt($userdata)) {
            $token = auth()->user()->createToken('Laravel9PassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}

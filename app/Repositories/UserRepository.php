<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function register($data)
    {
        $validator = Validator::make($data->all(), [
            'name' => 'required',
            'account' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'required' => '欄位沒有填寫完整!',
            'email.email' => '信箱格式錯誤',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 401);
        } else {
            try {
                $user = User::create([
                    'name' => $data->name,
                    'account' => $data->account,
                    'email' => $data->email,
                    'password' => bcrypt($data->password)
                ]);
                $token = $user->createToken('Laravel9PassportAuth')->accessToken;
                return response()->json(['token' => $token], 200);
            } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = $exception->errorInfo;
                if ($exception->getCode() === '23000') {
                    $errorInfo='帳號重複';
                }
                return response()->json(['errorInfo' => $errorInfo], 402);
            }
        }
    }

    public function login($data)
    {
        $userdata = [
            'account' => $data->account,
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

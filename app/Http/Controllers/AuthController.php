<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(Request $request)
    {
        return $this->userRepository->register($request);
    }

    public function login(Request $request)
    {
        return $this->userRepository->login($request);
    }

    public function userInfo(Request $request)
    {
         $user = auth()->user();
         return response()->json(['user' => $user], 200);

        //  return response()->json($request->user());
    }
    
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}

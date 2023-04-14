<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(StoreUserRequest $request)
    {
        try {
            $request->validated($request->all());

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->success([
                'user' => $user,
                'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 'Error occured', 500);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $request->validated($request->all());

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->error(null, 'Email or Password is incorrect', 401);
            }

            $user = User::where('email', $request->email)->first();

            return $this->success([
                'user' => $user,
                'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 'Error occured', 500);
        }
    }

    public function logout()
    {
        try {
            Auth::user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

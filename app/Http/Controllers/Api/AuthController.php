<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'in:owner,customer,admin'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'=>'success',
            'message'=>'register success',
            'data'=>[
                'user' => $user,
                'token' => $token,
            ]
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'status'=>'error',
                'message'=>'invalid credentials.'
            ]);
        };

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'=>'success',
            'message'=>'login success',
            'data' => [
                'user' => $user,
                'token' => $token
            ],
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'=>'success',
            'message'=>'logout success.'
        ], 200);
    }

    public function me(Request $request){
        return response()->json([
            'status'=>'success',
            'data'=>$request->user(),
        ], 200);
    }

    public function updateMe(Request $request){
        $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes|numeric',
            'avatar' => 'sometimes|nullable|image'
        ]);

        $request->user()->update($request->only([
            'name', 'phone', 'avatar'
        ]));

        return response()->json([
            'status'=>'success',
            'message'=>'update profile success',
            'data'=>$request->user()->fresh()
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:COMPANY,FREELANCER'
            ]);

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => strtoupper($request->role)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'        => true,
                'data'          => [
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'roleName'      => $request->role,
                    'access_token'  => $token,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'data'      => null
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'    => false,
                'message'   => 'email or password is incorrect',
                'data'      => null
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'        => true,
            'data'          => [
                'name'          => $user->name,
                'email'         => $user->email,
                'roleName'      => $user->role,
                'access_token'  => $token,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'    => true,
            'message' => 'Logged out successfully',
            'data' => null
        ]);
    }

    public function roles(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status'    => true,
            'message'   => 'Role list retrieved successfully',
            'data'      => $this->roleList()
        ]);
    }
}

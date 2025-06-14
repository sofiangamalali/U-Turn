<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;



class AuthService
{
    public function register($request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        return [
            'token' => $user->createToken('api-token')->plainTextToken
        ];
    }

    public function login($request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }
        return [
            'token' => $user->createToken('api-token')->plainTextToken
        ];
    }

    public function logout($request)
    {
        $request->user()->currentAccessToken()->delete();
    }

}
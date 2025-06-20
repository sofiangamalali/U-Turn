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
        $identifier = $request->identifier;

        $user = User::where(function ($query) use ($identifier) {
            $query->where('email', $identifier)
                ->orWhere('phone', $identifier);
        })->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return [
            'token' => $user->createToken('api-token')->plainTextToken
        ];
    }


    public function socialLogin($request)
    {
        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'provider' => $request->provider,
                'provider_id' => $request->provider_id
            ]
        );
        return [
            'token' => $user->createToken('api-token')->plainTextToken
        ];
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();
    }

}
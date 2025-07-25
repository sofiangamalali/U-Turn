<?php

namespace App\Services;

use App\Events\UserRegistered;
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
        event(new UserRegistered($user));
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
    public function changePassword($request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return ['message' => 'Password changed successfully'];
    }



    public function socialLogin($request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'provider' => $request->provider,
                'provider_id' => $request->provider_id,
            ]);
            event(new UserRegistered($user));
        } else {
            $user->update([
                'name' => $request->name,
                'provider' => $request->provider,
                'provider_id' => $request->provider_id,
            ]);
        }

        return [
            'token' => $user->createToken('api-token')->plainTextToken
        ];
    }


    public function logout()
    {
        request()->user()->currentAccessToken()->delete();
    }

}
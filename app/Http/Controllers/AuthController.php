<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Services\AuthService;
use App\Traits\HasApiResponse;
class AuthController extends Controller
{
    use HasApiResponse;

    public function __construct(protected AuthService $authService)
    {

    }
    public function register(RegisterRequest $request)
    {
        return $this->success($this->authService->register($request), 'User registered successfully');
    }

    public function login(LoginRequest $request)
    {
        return $this->success($this->authService->login($request), 'User logged in successfully');
    }
    public function socialLogin(SocialLoginRequest $request)
    {
        return $this->success($this->authService->socialLogin($request), 'User logged in successfully');
    }
    public function logout()
    {
        return $this->success($this->authService->logout(), 'User logged out successfully');
    }



}

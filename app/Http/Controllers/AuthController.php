<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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
    public function logout($request)
    {
        return $this->success($this->authService->logout($request), 'User logged out successfully');
    }



}

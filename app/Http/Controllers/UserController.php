<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ListingResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\HasApiResponse;


class UserController extends Controller
{
    use HasApiResponse;

    public function __construct(protected UserService $userService)
    {
    }

    public function listings()
    {
        return $this->success(ListingResource::collection($this->userService->getUserListings()));
    }


    public function getProfile()
    {
        return $this->success(UserResource::make($this->userService->getProfile()));
    }
    public function updateProfile(UpdateUserRequest $request)
    {
        $user = $this->userService->updateProfile($request->validated());
        return $this->success(UserResource::make($user), 'Profile updated successfully.');
    }

    public function deleteAccount(){

        $this->userService->deleteAccount();
        return $this->success([], 'Account deleted successfully.');
    }
}

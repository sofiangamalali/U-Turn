<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserImageRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ListingResource;
use App\Http\Resources\SellerProfileResource;
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


    public function getSellerProfile($id)
    {
        $seller = $this->userService->getSellerProfile($id);
        return $this->success(SellerProfileResource::make($seller));
    }

    public function updateProfileImage(UpdateUserImageRequest $request)
    {
        $image = $this->userService->uploadProfileImage($request->file('image'));
        return $this->success(ImageResource::make($image), 'Profile image updated successfully.');
    }
    public function deleteAccount()
    {

        $this->userService->deleteAccount();
        return $this->success([], 'Account deleted successfully.');
    }
}

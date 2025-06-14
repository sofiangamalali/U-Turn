<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListingResource;
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
}

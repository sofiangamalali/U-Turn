<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Services\PackageService;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;

class PackageController extends Controller
{
    use HasApiResponse;
    public function __construct(protected PackageService $packageService)
    {
    }

    public function getAll()
    {
        $packages = $this->packageService->getAll();
        return $this->success(PackageResource::collection($packages), 'Packages retrieved successfully.');
    }


}

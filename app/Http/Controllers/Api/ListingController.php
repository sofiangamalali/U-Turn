<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ListingService;
use App\Traits\HasApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    use HasApiResponse;
    use AuthorizesRequests;
    public function __construct(protected ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index(Request $request)
    {
        $listings = $this->listingService->filteredPaginated($request);
        return $this->paginated(ListingResource::collection($listings));
    }

    public function store(StoreListingRequest $request)
    {
        $listing = $this->listingService->create($request->validated());
        return $this->success(new ListingResource($listing), code: 201);
    }

    public function show($id)
    {
        $listing = $this->listingService->findById($id);

        return $this->success(new ListingResource($listing));
    }

    public function update(UpdateListingRequest $request, Listing $listing)
    {
        $this->authorize('update', Listing::class);
        $listing = $this->listingService->update($listing, $request->validated());
        return $this->success(new ListingResource($listing));
    }

    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);
        $this->listingService->delete($listing);
        return response()->json(['message' => 'Listing deleted']);
    }

}


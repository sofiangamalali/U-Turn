<?php
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::apiResource('listings', ListingController::class);
Route::get('/home', [GeneralController::class, 'home']);
Route::get('/transmission-types', [GeneralController::class, 'transmissionTypes']);
Route::get('/fuel-types', [GeneralController::class, 'fuelTypes']);
Route::get('/colors', [GeneralController::class, 'colors']);
Route::get('/steering-sides', [GeneralController::class, 'steeringSides']);
Route::get('/car-makes', [GeneralController::class, 'carMakes']);
Route::get('/conditions', [GeneralController::class, 'conditions']);
Route::get('/spare-part-categories', [GeneralController::class, 'sparePartCategories']);



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/listings', [UserController::class, 'listings']);
    Route::post('/logout', [AuthController::class, 'logout']);
});







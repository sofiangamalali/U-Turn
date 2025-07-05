<?php
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/home', [GeneralController::class, 'home']);
Route::get('/transmission-types', [GeneralController::class, 'transmissionTypes']);
Route::get('/fuel-types', [GeneralController::class, 'fuelTypes']);
Route::get('/colors', [GeneralController::class, 'colors']);
Route::get('/steering-sides', [GeneralController::class, 'steeringSides']);
Route::get('/car-makes', [GeneralController::class, 'carMakes']);
Route::get('/conditions', [GeneralController::class, 'conditions']);
Route::get('/body-types', [GeneralController::class, 'bodyTypes']);
Route::get('/spare-part-categories', [GeneralController::class, 'sparePartCategories']);
Route::get('/seller/profile/{id}', [UserController::class, 'getSellerProfile']);
Route::get('/packages', [PackageController::class, 'getAll']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/social', [AuthController::class, 'socialLogin']);
Route::get('listings', [ListingController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('listings', ListingController::class)->except(['index']);
    Route::get('/user/listings', [UserController::class, 'listings']);
    Route::get('/user/profile', [UserController::class, 'getProfile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/profile/image', [UserController::class, 'updateProfileImage']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/user/delete-account', [UserController::class, 'deleteAccount']);

    //CHATS
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
    Route::post('/chats/{chat}/block', [ChatController::class, 'block']);
    Route::post('/chats/{chat}/unblock', [ChatController::class, 'unblock']);
    Route::post('/chats/messages', [ChatController::class, 'sendMessage']);



});
Route::get('/chats/stream', [ChatController::class, 'streamChats']);
Route::get('/chats/stream/{chat}', [ChatController::class, 'streamChatMessages']);




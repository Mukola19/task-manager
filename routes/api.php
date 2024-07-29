<?php


use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {

    if (!app()->isProduction()) {
        Artisan::call('scribe:generate');
    }
    return view('scribe.index');
})->name('documentation');


Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('delete', [AuthController::class, 'delete']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('tasks', TaskController::class);

    Route::get('users', [UserController::class, 'users']);
    Route::get('users/{id}', [UserController::class, 'user']);
});




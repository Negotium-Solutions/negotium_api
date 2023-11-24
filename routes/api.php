<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProcessCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');

// Users routes
Route::get('/user/{id?}', [UserController::class, 'get'])->name('api.user');
Route::post('/user/create', [UserController::class, 'create'])->name('api.user.create');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('api.user.update');
Route::delete('/user/delete/{id?}', [UserController::class, 'delete'])->name('api.user.delete');

// Process Category routes
Route::get('/process-category/{id?}', [ProcessCategoryController::class, 'get'])->name('api.process-category');
Route::post('/process-category/create', [ProcessCategoryController::class, 'create'])->name('api.process-category.create');
Route::put('/process-category/update/{id}', [ProcessCategoryController::class, 'update'])->name('api.process-category.update');
Route::delete('/process-category/delete/{id?}', [ProcessCategoryController::class, 'delete'])->name('api.process-category.delete');

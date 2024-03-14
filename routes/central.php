<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;

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

Route::post('/auth/login-tenant', [AuthController::class, 'getTenantAndLogin'])->name('auth.login-tenant');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');

// Users routes
Route::get('/user/{id?}', [UserController::class, 'get'])->name('api.user');
Route::post('/user/create', [UserController::class, 'create'])->name('api.user.create');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('api.user.update');
Route::delete('/user/delete/{id?}', [UserController::class, 'delete'])->name('api.user.delete');

// Tenant routes
Route::get('/tenant/{id?}', [TenantController::class, 'get'])->name('api.tenant');
Route::post('/tenant/create', [TenantController::class, 'create'])->name('api.tenant.create');
Route::put('/tenant/update/{id}', [TenantController::class, 'update'])->name('api.tenant.update');
Route::delete('/tenant/delete/{id?}', [TenantController::class, 'delete'])->name('api.tenant.delete');

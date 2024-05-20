<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Tenant\ProcessCategoryController;
use App\Http\Controllers\Api\Tenant\ProcessController;
use App\Http\Controllers\Api\Tenant\ProcessStepController;
use App\Http\Controllers\Api\Tenant\StepController;
use App\Http\Controllers\Api\Tenant\ActivityController;
use App\Http\Controllers\Api\Tenant\DocumentController;
use App\Http\Controllers\Api\Tenant\SchemaController;
use App\Http\Controllers\Api\Tenant\SchemaDataStoreController;
use App\Http\Controllers\Api\Tenant\ClientController;
use App\Http\Controllers\Api\Tenant\ClientTypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

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

Route::group([
    'prefix' => '/{tenant}',
    'middleware' => [
        InitializeTenancyByPath::class
    ],
], function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');

    // Users routes
    Route::get('/user/{id?}', [UserController::class, 'get'])->name('api.user');
    Route::post('/user/create', [UserController::class, 'create'])->name('api.user.create');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('api.user.update');
    Route::delete('/user/delete/{id?}', [UserController::class, 'delete'])->name('api.user.delete');

    // Clients routes
    Route::get('/client/{id?}', [ClientController::class, 'get'])->name('api.client');
    Route::post('/client/create', [ClientController::class, 'create'])->name('api.client.create');
    Route::put('/client/update/{id}', [ClientController::class, 'update'])->name('api.client.update');
    Route::delete('/client/delete/{id?}', [ClientController::class, 'delete'])->name('api.client.delete');

    // Client Type routes
    Route::get('/client-type/{id?}', [ClientTypeController::class, 'get'])->name('api.client-type');
    Route::post('/client-type/create', [ClientTypeController::class, 'create'])->name('api.client-type.create');
    Route::put('/client-type/update/{id}', [ClientTypeController::class, 'update'])->name('api.client-type.update');
    Route::delete('/client-type/delete/{id?}', [ClientTypeController::class, 'delete'])->name('api.client-type.delete');

    // Process Category routes
    Route::get('/process-category/{id?}', [ProcessCategoryController::class, 'get'])->name('api.process-category');
    Route::post('/process-category/create', [ProcessCategoryController::class, 'create'])->name('api.process-category.create');
    Route::put('/process-category/update/{id}', [ProcessCategoryController::class, 'update'])->name('api.process-category.update');
    Route::delete('/process-category/delete/{id?}', [ProcessCategoryController::class, 'delete'])->name('api.process-category.delete');

    // Process routes
    Route::get('/process/{id?}', [ProcessController::class, 'get'])->name('api.process');
    Route::post('/process/create', [ProcessController::class, 'create'])->name('api.process.create');
    Route::put('/process/update/{id}', [ProcessController::class, 'update'])->name('api.process.update');
    Route::delete('/process/delete/{id?}', [ProcessController::class, 'delete'])->name('api.process.delete');

    // Process Steps routes
    Route::get('/process-step/{id?}', [ProcessStepController::class, 'get'])->name('api.process-step');
    Route::post('/process-step/create', [ProcessStepController::class, 'create'])->name('api.process-step.create');
    Route::put('/process-step/update/{id}', [ProcessStepController::class, 'update'])->name('api.process-step.update');
    Route::delete('/process-step/delete/{id?}', [ProcessStepController::class, 'delete'])->name('api.process-step.delete');

    // Steps routes
    Route::get('/step/{parent_id}/{id?}', [StepController::class, 'get'])->name('api.step');
    Route::post('/step/create/{parent_id}', [StepController::class, 'create'])->name('api.step.create');
    Route::put('/step/update/{id}', [StepController::class, 'update'])->name('api.step.update');
    Route::delete('/step/delete/{id?}', [StepController::class, 'delete'])->name('api.step.delete');

    // Activities routes
    Route::get('/activity/{step_id}/{id?}', [ActivityController::class, 'get'])->name('api.activity');
    Route::post('/activity/create/{step}', [ActivityController::class, 'create'])->name('api.activity.create');
    Route::post('/activity/create-schema/{model}', [ActivityController::class, 'createSchema'])->name('api.activity.create-schema');
    Route::put('/activity/update/{id}', [ActivityController::class, 'update'])->name('api.activity.update');
    Route::delete('/activity/delete/{id?}', [ActivityController::class, 'delete'])->name('api.activity.delete');

    // Documents routes
    Route::get('/document/{id?}', [DocumentController::class, 'get'])->name('api.document');
    Route::post('/document/create', [DocumentController::class, 'create'])->name('api.document.create');
    Route::post('/document/update/{id}', [DocumentController::class, 'update'])->name('api.document.update');
    Route::delete('/document/delete/{id?}', [DocumentController::class, 'delete'])->name('api.document.delete');

    // Schema routes
    Route::get('/schema/{id?}', [SchemaController::class, 'get'])->name('api.schema');
    Route::post('/schema/create', [SchemaController::class, 'create'])->name('api.schema.create');
    Route::put('/schema/update/{id}', [SchemaController::class, 'update'])->name('api.schema.update');
    Route::delete('/schema/delete/{id?}', [SchemaController::class, 'delete'])->name('api.schema.delete');
    Route::get('/schema/{id}/columns', [SchemaController::class, 'getColumns'])->name('api.schema.columns');

    // Schema Data Store routes
    Route::get('/schema-data-store/{id?}', [SchemaDataStoreController::class, 'get'])->name('api.schema-data-store');
    Route::post('/schema-data-store/create', [SchemaDataStoreController::class, 'create'])->name('api.schema-data-store.create');
    Route::put('/schema-data-store/update/{id}', [SchemaDataStoreController::class, 'update'])->name('api.schema-data-store.update');
    Route::delete('/schema-data-store/delete/{id?}', [SchemaDataStoreController::class, 'delete'])->name('api.schema-data-store.delete');
});

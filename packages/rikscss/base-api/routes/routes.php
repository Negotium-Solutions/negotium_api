<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Rikscss\BaseApi\Http\Controllers\Api\BaseApiLogController;

Route::group(['prefix' => 'api'], function () {
    Route::get('/base-api-log/{id?}', [BaseApiLogController::class, 'get'])->name('api.base-api-log');
    Route::post('/base-api-log/create', [BaseApiLogController::class, 'create'])->name('api.base-api-log.create');
    Route::put('/base-api-log/update/{id}', [BaseApiLogController::class, 'update'])->name('api.base-api-log.update');
    Route::delete('/base-api-log/delete/{id?}', [BaseApiLogController::class, 'delete'])->name('api.base-api-log.delete');
});

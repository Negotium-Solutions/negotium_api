<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Tenant\FormController;
use App\Http\Controllers\Api\Tenant\ProcessCategoryController;
use App\Http\Controllers\Api\Tenant\ProcessController;
use App\Http\Controllers\Api\Tenant\StepController;
use App\Http\Controllers\Api\Tenant\ActivityController;
use App\Http\Controllers\Api\Tenant\DynamicModelFieldTypeGroupController;
use App\Http\Controllers\Api\Tenant\DocumentController;
use App\Http\Controllers\Api\Tenant\SchemaController;
use App\Http\Controllers\Api\Tenant\SchemaDataStoreController;
use App\Http\Controllers\Api\Tenant\ProfileController;
use App\Http\Controllers\Api\Tenant\ProfileTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Tenant\NoteController;
use App\Http\Controllers\Api\Tenant\CommunicationController;
use App\Http\Controllers\Api\Tenant\LookUpController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Controllers\Api\Tenant\DynamicModelFieldController;
use App\Http\Controllers\Api\Tenant\ProcessExecutionController;
use App\Http\Controllers\Api\Tenant\DynamicModelController;
use App\Http\Controllers\Api\Tenant\DynamicModelCategoryController;
use App\Http\Controllers\Api\Tenant\DynamicModelStepController;

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

    // Profiles routes
    Route::get('/profile/{id?}', [ProfileController::class, 'get'])->name('api.profile');
    Route::get('/profile/dynamic-model/{id}', [ProfileController::class, 'getDynamicModel'])->name('api.profile.dynamic-model');
    Route::post('/profile/create', [ProfileController::class, 'create'])->name('api.profile.create');
    Route::post('/profile/update/{id}', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::delete('/profile/delete/{id?}', [ProfileController::class, 'delete'])->name('api.profile.delete');
    Route::post('/profile/assign-processes', [ProfileController::class, 'assignProcesses'])->name('api.profile.assign-processes');
    Route::delete('/profile/delete-process', [ProfileController::class, 'deleteProcess'])->name('api.profile.delete-process');

    Route::get('/profile/schema/{profile_type_id}', [ProfileController::class, 'getSchema'])->name('api.profile.schema');

    // Profile Type routes
    Route::get('/profile-type/{id?}', [ProfileTypeController::class, 'get'])->name('api.profile-type');
    Route::post('/profile-type/create', [ProfileTypeController::class, 'create'])->name('api.profile-type.create');
    Route::put('/profile-type/update/{id}', [ProfileTypeController::class, 'update'])->name('api.profile-type.update');
    Route::delete('/profile-type/delete/{id?}', [ProfileTypeController::class, 'delete'])->name('api.profile-type.delete');

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
    Route::post('/process/update-process-log-status', [ProcessController::class, 'updateProcessLogStatus'])->name('api.process.update-process-log-status');

    // Process Execution routes
    Route::get('/process-execution', [ProcessExecutionController::class, 'get'])->name('api.process-execution');

    // Steps routes
    Route::get('/step/{parent_id}/{id?}/{model_id?}', [StepController::class, 'get'])->name('api.step');
    Route::post('/step/create', [StepController::class, 'create'])->name('api.step.create');
    Route::put('/step/update/{id}', [StepController::class, 'update'])->name('api.step.update');
    Route::delete('/step/delete/{id?}', [StepController::class, 'delete'])->name('api.step.delete');

    // Activities routes
    Route::get('/activity/{step_id}/{id?}', [ActivityController::class, 'get'])->name('api.activity');
    Route::post('/activity/create/{step}', [ActivityController::class, 'create'])->name('api.activity.create');
    Route::post('/activity/create-schema/{model}', [ActivityController::class, 'createSchema'])->name('api.activity.create-schema');
    Route::put('/activity/update/{id}', [ActivityController::class, 'update'])->name('api.activity.update');
    Route::delete('/activity/delete/{id?}', [ActivityController::class, 'delete'])->name('api.activity.delete');

    // Activity Group routes
    Route::get('/dynamic-model-field-type-group/{id?}', [DynamicModelFieldTypeGroupController::class, 'get'])->name('api.dynamic-model-field-type-group');

    // Documents routes
    Route::get('/document/{id?}', [DocumentController::class, 'get'])->name('api.document');
    Route::post('/document/create', [DocumentController::class, 'create'])->name('api.document.create');
    Route::post('/document/update/{id}', [DocumentController::class, 'update'])->name('api.document.update');
    Route::delete('/document/delete/{id?}', [DocumentController::class, 'delete'])->name('api.document.delete');
    Route::get('/document/download/{id?}', [DocumentController::class, 'download'])->name('api.document.download');

    // Notes routes
    Route::get('/note/{id?}', [NoteController::class, 'get'])->name('api.note');
    Route::post('/note/create', [NoteController::class, 'create'])->name('api.note.create');
    Route::post('/note/update/{id}', [NoteController::class, 'update'])->name('api.note.update');
    Route::delete('/note/delete/{id?}', [NoteController::class, 'delete'])->name('api.note.delete');

    // Communications routes
    Route::get('/communication/{id?}', [CommunicationController::class, 'get'])->name('api.communication');
    Route::post('/communication/create', [CommunicationController::class, 'create'])->name('api.communication.create');
    Route::post('/communication/update/{id}', [CommunicationController::class, 'update'])->name('api.communication.update');
    Route::delete('/communication/delete/{id?}', [CommunicationController::class, 'delete'])->name('api.communication.delete');
    Route::post('/communication/send-email/{id}', [CommunicationController::class, 'sendEmail'])->name('api.communication.send-email');
    Route::post('/communication/send-sms/{id}', [CommunicationController::class, 'sendSMS'])->name('api.communication.send-sms');
    Route::post('/communication/send-whatsapp/{id}', [CommunicationController::class, 'sendWhatsApp'])->name('api.communication.send-whatsapp');

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

    // Form routes
    Route::get('/form/{id?}', [FormController::class, 'get'])->name('api.form');
    Route::post('/form/create', [FormController::class, 'create'])->name('api.form.create');
    Route::put('/form/update/{id}', [FormController::class, 'update'])->name('api.form.update');
    Route::delete('/form/delete/{id?}', [FormController::class, 'delete'])->name('api.form.delete');

    // Lookup routes
    Route::get('/lookup', [LookUpController::class, 'get'])->name('api.lookup');

    // Dynamic Models - Start
    Route::get('/dynamic-model/{id?}', [DynamicModelController::class, 'get'])->name('api.dynamic-model');
    Route::get('/dynamic-model/new-empty-record/{schema_id}', [DynamicModelController::class, 'newRecord'])->name('api.dynamic-model.new-empty-record');
    Route::get('/dynamic-model/group/{id?}', [DynamicModelController::class, 'getGroup'])->name('api.dynamic-model.group');

    // Dynamic Model Field routes
    Route::post('/dynamic-field/update', [DynamicModelFieldController::class, 'updateFields'])->name('api.dynamic-field.update');

    // Dynamic Model Field routes
    Route::post('/dynamic-model-field/create', [DynamicModelFieldController::class, 'create'])->name('api.dynamic-model-field.create');
    Route::get('/dynamic-model-field/{step_id?}/{id?}', [DynamicModelFieldController::class, 'get'])->name('api.dynamic-model-field');
    Route::delete('/dynamic-model-field/delete/{id?}', [DynamicModelFieldController::class, 'delete'])->name('api.dynamic-model-field.delete');

    Route::get('/dynamic-model-category/{id?}', [DynamicModelCategoryController::class, 'get'])->name('api.dynamic-model-category');
    Route::post('/dynamic-model/profile-capture/create', [ProfileController::class, 'createProfile'])->name('api.dynamic-model.profile-capture.create');

    // Dynamic Model Step routes
    Route::post('/dynamic-model-step/create', [DynamicModelStepController::class, 'create'])->name('api.dynamic-model-step.create');
    // Dynamic Models - End
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['cors'])->group(function () {


    Route::post('/projects', [App\Http\Controllers\ApiController::class, 'allProjects']);
    Route::get('/projectId', [App\Http\Controllers\ApiController::class, 'projectID']);
    Route::post('/project/edit', [App\Http\Controllers\ApiController::class, 'projectEdit']);
    Route::get('/project/delete', [App\Http\Controllers\ApiController::class, 'projectDelete']);


    Route::post('/addproject', [App\Http\Controllers\ApiController::class, 'addProject'])->name('addproject');

    Route::get('/shifts', [App\Http\Controllers\ApiController::class, 'shifts'])->name('shifts');

    Route::get('/shiftId', [App\Http\Controllers\ApiController::class, 'oneShifts'])->name('shift-edit');
    Route::post('/shift/edit', [App\Http\Controllers\ApiController::class, 'oneShiftsEdit'])->name('shift-edit-submit');
    Route::get('/shift/delete', [App\Http\Controllers\ApiController::class, 'shiftDelete']);
    Route::get('/shift/delworker', [App\Http\Controllers\ApiController::class, 'deleteShiftWorker']);
    Route::post('/shift/addWorker', [App\Http\Controllers\ApiController::class, 'addWorkerShift']);

    Route::get('/pages/userShift', [App\Http\Controllers\ApiController::class, 'userShift'])->name('userShift');
    Route::post('/openShift', [App\Http\Controllers\ApiController::class, 'openShift'])->name('openShift');
    Route::post('/addShift', [App\Http\Controllers\ApiController::class, 'addShift'])->name('addShift');


    Route::get('/listManagers', [App\Http\Controllers\ApiController::class, 'listManagers']);

    Route::get('/infoUser', [App\Http\Controllers\ApiController::class, 'infoUser']);
    Route::get('/deactivateUser', [App\Http\Controllers\ApiController::class, 'deactive']);
    Route::get('/activatedUser', [App\Http\Controllers\ApiController::class, 'activeUser']);


    Route::post('/newWorker', [App\Http\Controllers\ApiController::class, 'newWorker'])->name('newWorker');
    Route::get('/listWorkers', [App\Http\Controllers\ApiController::class, 'listWorkers']);
    Route::post('/worker/edit', [App\Http\Controllers\ApiController::class, 'workerEdit'])->name('workerEdit');
    Route::get('/worker', [App\Http\Controllers\ApiController::class, 'worker'])->name('worker');
    Route::get('/worker/delete', [App\Http\Controllers\ApiController::class, 'workerDelete'])->name('deleteWorker');


    Route::post('/register', [App\Http\Controllers\ApiController::class, 'register'])->name('register');
    Route::post('/login', [App\Http\Controllers\ApiController::class, 'login']);

//    Route::post('logout', [App\Http\Controllers\ApiController::class, 'logout']);

    Route::get('/send-email', [App\Http\Controllers\ApiController::class, 'sendEmail'])->name('sendEmail');

    Route::post('/messages', [App\Http\Controllers\ApiController::class, 'message'])->name('message');

    Route::post('/shift-request', [App\Http\Controllers\ApiController::class, 'shiftRequest']);
});
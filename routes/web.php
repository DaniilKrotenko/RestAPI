<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['cors'])->group(function () {
    Route::get('/chat/{id?}', [App\Http\Controllers\ApiController::class, 'chat'])->name('chat');
});


//Route::get('/allprojects', [App\Http\Controllers\HomeController::class, 'allProjects'])->name('allprojects');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

<?php

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

use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return view('auth.login');
})->name('main');

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
});


Route::group(['middleware' => 'auth:user'], function () {
    Route::get('create', [UserController::class, 'create'])->name('user.add');
    Route::post('create', [UserController::class, 'store'])->name('user.create');
    Route::get('/list', [UserController::class, 'index'])->name('user.index');
    Route::get('show/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('show/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('delete/{id}', [UserController::class, 'destroy']);
});

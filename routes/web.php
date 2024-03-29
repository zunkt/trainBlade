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

use App\Http\Controllers\Client\User\AuthController;
use App\Http\Controllers\Client\User\UserController;
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

    Route::group(['middleware' => 'admin'], function () {
        Route::get('phplot', [UserController::class, 'phplot'])->name('phplot.index');
        Route::get('show/{id}', [UserController::class, 'show'])->name('user.show');
        Route::post('show/{id}', [UserController::class, 'update'])->name('user.update');
        Route::get('create', [UserController::class, 'create'])->name('user.add');
        Route::post('create', [UserController::class, 'store'])->name('user.create');
        Route::get('/list', [UserController::class, 'index'])->name('user.index');
        Route::post('delete', [UserController::class, 'destroy'])->name('user.delete');
    });

    Route::get('/list', [UserController::class, 'index'])->name('user.index');
    Route::get('show/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('show/{id}', [UserController::class, 'update'])->name('user.update');
});

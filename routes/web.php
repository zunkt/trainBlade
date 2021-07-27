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

use App\Http\Controllers\Client\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $pages = intval($request->size);
    $users = \App\User::all();
    return view('welcome', ['users' => $users]);
});

Route::get('/all', [UserController::class, 'index']);

Route::get('/user', [UserController::class, 'index'])->name('articles.index');
Route::post('/user', [UserController::class, 'store']);
Route::get('/user/create', [UserController::class, 'create']);

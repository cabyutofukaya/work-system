<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TodoController;
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


// ログイン
Route::get('/login', LoginController::class)->name("login");

Route::group(['middleware' => 'auth'], function () {
    // Todo
    Route::resource('/todos', TodoController::class)->except(["show", "create", "edit"]);
    //完了切り替え
    Route::patch('/todos/{todo}/toggle-done', [TodoController::class, 'toggleDone'])->name('todos.toggleDone');
});

Route::fallback(function () {
    return redirect()->route('login');
});

<?php

use App\Http\Controllers\FrontEnd\CategoriesController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// routes/web.php
Route::name('categories.')->prefix('frontend/category')->group(function () {
    Route::get('/', [CategoriesController::class, 'index'])->name('index');
    Route::get('/{id}', [CategoriesController::class, 'show'])->whereNumber('id')->name('show');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

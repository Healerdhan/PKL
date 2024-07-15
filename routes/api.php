<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SiswaController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('category.')->prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{id}', [CategoryController::class, 'show'])->whereNumber('id')->name('show');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::put('/{id}', [CategoryController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->whereNumber('id')->name('destroy');
    Route::post('/delete-multiple', [CategoryController::class, 'destroyMultiple'])->name('destroyMultiple');
});

Route::name('siswa.')->prefix('siswa')->group(function () {
    Route::get('/', [SiswaController::class, 'index'])->name('index');
    Route::get('/{id}', [SiswaController::class, 'show'])->name('show');
    Route::post('/', [SiswaController::class, 'store'])->name('store');
    Route::put('/{id}', [SiswaController::class, 'update'])->name('update');
    Route::delete('/{id}', [SiswaController::class, 'destroy'])->name('destroy');
    Route::post('/delete-multiple', [SiswaController::class, 'destroyMultiple'])->name('destroyMultiple');
});

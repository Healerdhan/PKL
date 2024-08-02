<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DudiController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SubjectController;
use App\Models\Sertifikat;
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

Route::name('dudi.')->prefix('dudi')->group(function () {
    Route::get('/', [DudiController::class, 'index'])->name('index');
    Route::get('/{id}', [DudiController::class, 'show'])->name('show');
    Route::post('/', [DudiController::class, 'store'])->name('store');
    Route::put('/{id}', [DudiController::class, 'update'])->name('update');
    Route::delete('/{id}', [DudiController::class, 'destroy'])->name('destroy');
    Route::post('/delete-multiple', [DudiController::class, 'destroyMultiple'])->name('destroyMultiple');
});


Route::name('pembimbing.')->prefix('pembimbing')->group(function () {
    Route::get('/', [PembimbingController::class, 'index'])->name('index');
    Route::get('/{id}', [PembimbingController::class, 'show'])->name('show');
    Route::post('/', [PembimbingController::class, 'store'])->name('store');
    Route::put('/{id}', [PembimbingController::class, 'update'])->name('update');
    Route::delete('/{id}', [PembimbingController::class, 'destroy'])->name('destroy');
    Route::post('/delete-multiple', [PembimbingController::class, 'destroyMultiple'])->name('destroyMultiple');
});

Route::name('subjec.')->prefix('subjec')->group(function () {
    Route::get('/', [SubjectController::class, 'index'])->name('index');
    Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
    Route::post('/', [SubjectController::class, 'store'])->name('store');
    Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
    Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    Route::post('/delete-multiple', [SubjectController::class, 'destroyMultiple'])->name('destroyMultiple');
});

Route::name('nilai.')->prefix('nilai')->group(function () {
    Route::get('/', [NilaiController::class, 'index'])->name('index');
    Route::get('/{id}', [NilaiController::class, 'show'])->name('show');
    Route::post('/', [NilaiController::class, 'store'])->name('store');
    Route::put('/{id}', [NilaiController::class, 'update'])->name('update');
    Route::delete('/{id}', [NilaiController::class, 'destroy'])->name('destroy');
    Route::post('/delete-multiple', [NilaiController::class, 'destroyMultiple'])->name('destroyMultiple');
});

Route::name('sertifikat.')->prefix('sertifikat')->group(function () {
    Route::get('/', [SertifikatController::class, 'index'])->name('index');
    Route::get('/{id}', [SertifikatController::class, 'show'])->name('show');
    Route::post('/', [SertifikatController::class, 'store'])->name('store');
    Route::put('/{id}', [SertifikatController::class, 'update'])->name('update');
    Route::delete('/{id}', [SertifikatController::class, 'destroy'])->name('destroy');
    Route::post('/delete-multiple', [SertifikatController::class, 'destroyMultiple'])->name('destroyMultiple');
});

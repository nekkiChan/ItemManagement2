<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('items')->group(function () {
    Route::get('/', [App\Http\Controllers\ItemController::class, 'index'])->name('items.index');
    Route::get('/archive', [App\Http\Controllers\ItemController::class, 'archive'])->name('items.archive');
    Route::get('/search', [App\Http\Controllers\ItemController::class, 'search'])->name('items.search');
    Route::get('/add', [App\Http\Controllers\ItemController::class, 'add']);
    Route::post('/add', [App\Http\Controllers\ItemController::class, 'add']);
    Route::get('/edit', [App\Http\Controllers\ItemController::class, 'edit'])->name('items.edit');
    Route::post('/edit', [App\Http\Controllers\ItemController::class, 'edit']);
    Route::get('/convert_status', [App\Http\Controllers\ItemController::class, 'convertStatus'])->name('items.convert_status');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;


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

Route::get('/', [AuthController::class, 'index']);
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'store'])->name('login');
});

Route::middleware(['auth.custom'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('logout', [AuthController::class, 'destroy'])->name('logout');
    });
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('product');
        Route::post('getData', [ProductController::class, 'getData']);
        Route::get('add', [ProductController::class, 'add']);
        Route::post('insert', [ProductController::class, 'insert']);
        Route::get('edit/{id}', [ProductController::class, 'edit']);
        Route::post('update/{id}', [ProductController::class, 'update']);
        Route::get('delete/{id}', [ProductController::class, 'delete']);
        Route::get('export', [ProductController::class, 'export']);
    });
    Route::prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('category');
        Route::post('getData', [CategoryController::class, 'getData']);
        Route::get('add', [CategoryController::class, 'add']);
        Route::post('insert', [CategoryController::class, 'insert']);
        Route::get('edit/{id}', [CategoryController::class, 'edit']);
        Route::post('update/{id}', [CategoryController::class, 'update']);
        Route::get('delete/{id}', [CategoryController::class, 'delete']);
        Route::get('export', [CategoryController::class, 'export']);
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::get('edit/{id}', [ProfileController::class, 'edit']);
        Route::post('update/{id}', [ProfileController::class, 'update']);
        Route::get('delete/{id}', [ProfileController::class, 'delete']);
    });
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::post('getData', [UserController::class, 'getData']);
        Route::get('add', [UserController::class, 'add']);
        Route::post('insert', [UserController::class, 'insert']);
        Route::get('edit/{id}', [UserController::class, 'edit']);
        Route::post('update/{id}', [UserController::class, 'update']);
        Route::get('delete/{id}', [UserController::class, 'delete']);
        Route::get('export', [UserController::class, 'export']);
    });
    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('role');
        Route::post('getData', [RoleController::class, 'getData']);
        Route::get('add', [RoleController::class, 'add']);
        Route::post('insert', [RoleController::class, 'insert']);
        Route::get('edit/{id}', [RoleController::class, 'edit']);
        Route::post('update/{id}', [RoleController::class, 'update']);
        Route::get('delete/{id}', [RoleController::class, 'delete']);
    });
});

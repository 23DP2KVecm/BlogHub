<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

// ── Publiskie maršruti ────────────────────────────────────────
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{raksts:slug}', [PostController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [\App\Http\Controllers\TagController::class, 'index']);
Route::get('/stats', [StatsController::class, 'index']);

// ── Autentifikācija ───────────────────────────────────────────
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// ── Autorizēti maršruti ───────────────────────────────────────
Route::middleware('auth.token')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/posts/{id}/comments', [PostController::class, 'storeComment']);
    Route::post('/posts/{id}/reakcija', [PostController::class, 'storeReakcija']);

    Route::get('/dashboard/posts', [DashboardController::class, 'index']);
    Route::post('/dashboard/posts', [DashboardController::class, 'store']);
    Route::put('/dashboard/posts/{id}', [DashboardController::class, 'update']);
    Route::delete('/dashboard/posts/{id}', [DashboardController::class, 'destroy']);
});

// ── Administratora maršruti ───────────────────────────────────
Route::middleware('auth.token:administrators')->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::put('/admin/users/{id}/role', [AdminController::class, 'updateUserRole']);
    Route::get('/admin/roles', [AdminController::class, 'roles']);
    Route::get('/admin/posts', [AdminController::class, 'posts']);
    Route::delete('/admin/posts/{id}', [AdminController::class, 'deletePost']);
    Route::get('/admin/comments', [AdminController::class, 'comments']);
    Route::patch('/admin/comments/{id}/approve', [AdminController::class, 'approveComment']);
    Route::delete('/admin/comments/{id}', [AdminController::class, 'deleteComment']);
});

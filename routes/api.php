<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Todo lo de este archivo cuelga de /api automaticamente.

// Publico: leer avisos, como tu portada pero en JSON.
Route::get('/avisos', [PostController::class, 'index']);
Route::get('/avisos/{post}', [PostController::class, 'show']);

// Entregar un token a quien traiga credenciales correctas.
Route::post('/token', [TokenController::class, 'crear']);

// Con token: escribir, y saber quien eres.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/yo', fn (Request $request) => $request->user());
    Route::post('/avisos', [PostController::class, 'store']);
    Route::put('/avisos/{post}', [PostController::class, 'update']);
    Route::delete('/avisos/{post}', [PostController::class, 'destroy']);
    Route::post('/token/revocar', [TokenController::class, 'revocar']);
});

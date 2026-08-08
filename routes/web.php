<?php

use App\Http\Controllers\FoodController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Consulta del catalogo
    Route::get('/foods', [FoodController::class, 'index'])
        ->name('foods.index');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/foods', [FoodController::class, 'index'])
        ->name('foods.index');

    Route::get('/foods/create', [FoodController::class, 'create'])
        ->name('foods.create');

    Route::post('/foods', [FoodController::class, 'store'])
        ->name('foods.store');

    Route::get('/foods/{food}/edit', [FoodController::class, 'edit'])
        ->name('foods.edit');

    Route::put('/foods/{food}', [FoodController::class, 'update'])
        ->name('foods.update');

    Route::patch('/foods/{food}/toggle-status', [FoodController::class, 'toggleStatus'])
        ->name('foods.toggle-status');
});



require __DIR__.'/auth.php';
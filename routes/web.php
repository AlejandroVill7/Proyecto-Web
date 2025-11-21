<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Juez\JuezController;
use App\Http\Controllers\Participante\ParticipanteController;

Route::get('/', function () {
    return view('welcome');
});

//Rutas comunes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//--RUTAS ADMINISTRADOR--
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
});

//--RTUAS JUEZ--
Route::middleware(['auth', 'role:Juez'])->prefix('juez')->name('juez.')->group(function () {
    Route::get('/dashboard', [JuezController::class, 'index'])->name('dashboard');
});


//--RUTAS PARTICIPANTE--
Route::middleware(['auth', 'role:Participante'])->prefix('participante')->name('participante.')->group(function () {

    Route::get('/registro-inicial',[ParticipanteController::class, 'index'])->name('registro.inicial');

    Route::get('/dashboard', [ParticipanteController::class, 'index'])->name('dashboard');
});



require __DIR__.'/auth.php';

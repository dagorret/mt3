<?php

use App\Livewire\Unidades\FormUnidad;
use App\Livewire\Unidades\IndexUnidad;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Grupo de Unidades
    Route::prefix('unidades')->name('unidades.')->group(function () {
        Route::get('/', IndexUnidad::class)->name('index');
        Route::get('/crear', FormUnidad::class)->name('crear');
        Route::get('/{unidad}/editar', FormUnidad::class)
            ->whereNumber('unidad')
            ->name('editar');
    });
});

require __DIR__.'/settings.php';
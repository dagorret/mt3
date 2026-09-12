<?php

use App\Livewire\Unidades\FormUnidad;
use App\Livewire\Unidades\IndexUnidad;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/test-inputs', function () {
    return view('components.⚡test-inputs');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Grupo de Unidades
    Route::prefix('unidades')->name('unidades.')->group(function () {
        Route::get('/', App\Livewire\Unidades\IndexUnidad::class)->name('index');
        Route::get('/crear', App\Livewire\Unidades\FormUnidad::class)->name('crear');
        Route::get('/{unidad}/editar', App\Livewire\Unidades\FormUnidad::class)
            ->whereNumber('unidad')
            ->name('editar');
    });
});

require __DIR__.'/settings.php';
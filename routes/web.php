<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\ReciboPago;

// Ruta principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Ruta para mostrar los recibos
Route::get('/recibos', function () {
    $recibos = ReciboPago::all();
    return view('recibo_pagos.index', compact('recibos'));
})->name('recibos');

// Dashboard protegido
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';

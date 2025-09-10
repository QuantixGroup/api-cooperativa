<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReciboPagoController;

Route::apiResource('recibo-pagos', ReciboPagoController::class);
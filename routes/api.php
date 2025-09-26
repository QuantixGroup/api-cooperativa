<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HoraTrabajoController;
use App\Http\Controllers\ComprobantePagoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth.usuarios')->group(function () {

    Route::post('/horas', [HoraTrabajoController::class, 'InsertarHoras']);
    Route::post('/comprobantes', [ComprobantePagoController::class, 'InsertarComprobante']);
});


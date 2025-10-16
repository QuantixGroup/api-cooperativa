<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HoraTrabajoController;
use App\Http\Controllers\ComprobantePagoController;
use App\Http\Middleware\AutenticacionDesdeApiUsuarios;



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

Route::middleware([AutenticacionDesdeApiUsuarios::class])->group(function () {

    Route::get('/horas', [HoraTrabajoController::class, 'ObtenerHoras']);
    Route::post('/horas/registro', [HoraTrabajoController::class, 'InsertarHoras']);
    Route::post('/comprobantes', [ComprobantePagoController::class, 'InsertarComprobante']);
});

Route::get('/recibos/{cedula}', [ComprobantePagoController::class, 'ObtenerRecibosPorCedula']);
Route::put('/recibos/{idPago}', [ComprobantePagoController::class, 'ActualizarEstadoRecibo']);
Route::get('/recibos/{idPago}/pdf', [ComprobantePagoController::class, 'ObtenerPdfRecibo']);


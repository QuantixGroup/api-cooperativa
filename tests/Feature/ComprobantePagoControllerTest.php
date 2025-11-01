<?php

use App\Http\Controllers\ComprobantePagoController;
use App\Models\ComprobantePago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

test('insertar comprobante requiere cedula', function () {
    $controller = new ComprobantePagoController;
    $request = Request::create('/', 'POST', ['monto' => 100, 'fecha_comprobante' => '2025-01-01']);

    $response = $controller->InsertarComprobante($request);

    expect($response->status())->toBe(401);
});

test('insertar comprobante crea registro y devuelve 201', function () {
    $controller = new ComprobantePagoController;
    $request = Request::create('/', 'POST', ['monto' => 1500, 'fecha_comprobante' => '2025-01-02']);
    $request->attributes->set('cedula', 123456789);
    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
    $response = $controller->InsertarComprobante($request);
    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
    expect($response->status())->toBe(201);
    $data = $response->getData();
    expect($data->success)->toBeTrue();
    expect(ComprobantePago::where('cedula', 123456789)->exists())->toBeTrue();
});

test('obtener recibos por cedula devuelve lista', function () {
    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
    ComprobantePago::create([
        'cedula' => 111111111,
        'monto' => 1000,
        'fecha_comprobante' => '2025-11-01',
        'archivo_comprobante' => null,
        'estado' => 'pendiente',
        'mes' => 10,
        'anio' => 2025,
    ]);

    ComprobantePago::create([
        'cedula' => 111111111,
        'monto' => 2000,
        'fecha_comprobante' => '2025-11-02',
        'archivo_comprobante' => null,
        'estado' => 'pendiente',
        'mes' => 11,
        'anio' => 2025,
    ]);

    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    $controller = new ComprobantePagoController;

    $response = $controller->ObtenerRecibosPorCedula(111111111);

    expect($response->status())->toBe(200);
    $body = $response->getData();
    expect(count($body))->toBeGreaterThanOrEqual(2);
});

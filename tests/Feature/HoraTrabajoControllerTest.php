<?php

use App\Http\Controllers\HoraTrabajoController;
use App\Models\HoraTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

test('obtener horas requiere cedula', function () {
    $controller = new HoraTrabajoController;
    $request = Request::create('/', 'GET');

    $response = $controller->ObtenerHoras($request);

    expect($response->status())->toBe(401);
});

test('insertar horas crea un registro y devuelve 201', function () {
    $controller = new HoraTrabajoController;
    $payload = ['fecha' => '2025-02-01', 'conteo_de_horas' => 4];
    $request = Request::create('/', 'POST', $payload);
    $request->attributes->set('cedula', 222333444);
    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
    $response = $controller->InsertarHoras($request);
    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    expect($response->status())->toBe(201);

    $body = $response->getData();
    expect($body->cedula)->toBe(222333444);

    expect(HoraTrabajo::where('cedula', 222333444)->exists())->toBeTrue();
});

test('no permite duplicados de horas el mismo dia/tipo', function () {
    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
    HoraTrabajo::create([
        'cedula' => 333222111,
        'fecha' => '2025-03-01',
        'conteo_de_horas' => 3,
        'tipo_trabajo' => null,
        'estado' => 'pendiente',
    ]);

    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    $controller = new HoraTrabajoController;
    $request = Request::create('/', 'POST', ['fecha' => '2025-03-01', 'conteo_de_horas' => 2]);
    $request->attributes->set('cedula', 333222111);

    $response = $controller->InsertarHoras($request);

    expect($response->status())->toBe(409);
});

<?php

use App\Models\ComprobantePago;

test('Model ComprobantePago tiene tabla, fillable y casts esperados', function () {
    $model = new ComprobantePago();

    expect($model->getTable())->toBe('pagos_mensuales');

    $fillable = $model->getFillable();
    expect($fillable)->toContain('cedula')
        ->and($fillable)->toContain('monto')
        ->and($fillable)->toContain('fecha_comprobante');

    $casts = $model->getCasts();
    expect(array_key_exists('monto', $casts))->toBeTrue()
        ->and(array_key_exists('fecha_comprobante', $casts))->toBeTrue();
});

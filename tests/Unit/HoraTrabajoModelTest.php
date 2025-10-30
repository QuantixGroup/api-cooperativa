<?php

use App\Models\HoraTrabajo;

test('Model HoraTrabajo tiene atributos fillable esperados', function () {
    $model = new HoraTrabajo();

    $fillable = $model->getFillable();

    expect($fillable)->toContain('cedula')
        ->and($fillable)->toContain('fecha')
        ->and($fillable)->toContain('conteo_de_horas');
});

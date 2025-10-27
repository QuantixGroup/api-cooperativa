<?php

use App\Models\User;

test('Model User tiene atributos fillable esperados', function () {
    $user = new User();

    $fillable = $user->getFillable();

    expect($fillable)->toContain('name')
        ->and($fillable)->toContain('email')
        ->and($fillable)->toContain('password');
});

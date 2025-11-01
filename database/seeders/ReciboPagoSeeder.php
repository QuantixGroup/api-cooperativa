<?php

namespace Database\Seeders;

use App\Models\ComprobantePago;
use Illuminate\Database\Seeder;

class ReciboPagoSeeder extends Seeder
{
    public function run()
    {
        ComprobantePago::create([
            'cedula' => 123456789,
            'monto' => 100.50,
            'fecha_comprobante' => now(),
            'estado' => 'pendiente',
            'mes' => 11,
            'anio' => 2025,
        ]);
    }
}

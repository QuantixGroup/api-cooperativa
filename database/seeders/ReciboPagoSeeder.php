<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReciboPago;

class ReciboPagoSeeder extends Seeder
{
    public function run()
    {
        ReciboPago::create([
            'usuario_id' => 1,
            'monto' => 100.50,
            'fecha_pago' => now(),
            'estado' => 'pagado',
        ]);
    }
}

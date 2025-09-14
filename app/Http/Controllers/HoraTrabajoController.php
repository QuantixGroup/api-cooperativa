<?php

namespace App\Http\Controllers;

use App\Models\HoraTrabajo;
use Illuminate\Http\Request;

class HoraTrabajoController extends Controller
{
       public function InsertarHoras(Request $request)
    {
        $cedula = $request->attributes->get('cedula');
        if (!$cedula) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'fecha'                => ['required','date'],
            'conteo_de_horas'      => ['required','integer','min:0','max:12'],
            'comprobante_compensacion' => ['nullable','string','max:255'],
            'monto_compensacion'   => ['nullable','numeric','min:0'],
            'fecha_compensacion'   => ['nullable','date'],
        ]);

        $yaExiste = HoraTrabajo::where('cedula', $cedula)
                    ->whereDate('fecha', $data['fecha'])
                    ->exists();
        if ($yaExiste) {
            return response()->json(['message' => 'Ya registraste horas para esa fecha.'], 409);
        }

        $registro = HoraTrabajo::create([
            'cedula'                   => $cedula,
            'fecha'                    => $data['fecha'],
            'conteo_de_horas'          => $data['conteo_de_horas'],
            'comprobante_compensacion' => $data['comprobante_compensacion'] ?? null,
            'monto_compensacion'       => $data['monto_compensacion'] ?? null,
            'fecha_compensacion'       => $data['fecha_compensacion'] ?? null,
            'estado'                   => 'pendiente', 
        ]);

        return response()->json($registro, 201);
    }
}

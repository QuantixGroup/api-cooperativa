<?php

namespace App\Http\Controllers;

use App\Models\ComprobantePago;
use Illuminate\Http\Request;

class ComprobantePagoController extends Controller
{
    public function InsertarComprobante(Request $request)
    {
        $cedula = $request->attributes->get('cedula');
        if (!$cedula)
            return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0'],
            'fecha_comprobante' => ['required', 'date'],
            'archivo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $ruta = null;
        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('comprobantes/pagos', 'public');
        }

        $comprobante = ComprobantePago::create([
            'cedula' => $cedula,
            'monto' => $data['monto'],
            'fecha_comprobante' => $data['fecha_comprobante'],
            'archivo_ruta' => $ruta,
            'estado' => 'pendiente',
        ]);

        return response()->json($comprobante, 201);
    }
}

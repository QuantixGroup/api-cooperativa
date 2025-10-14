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
            'archivo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'mes' => ['nullable', 'integer', 'min:1', 'max:12'],
            'anio' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $ruta = null;
        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('comprobantes/pagos', 'public');
        }

        $comprobante = ComprobantePago::create([
            'cedula' => $cedula,
            'monto' => $data['monto'],
            'fecha_comprobante' => $data['fecha_comprobante'],
            'archivo_comprobante' => $ruta,
            'estado' => 'pendiente',
            'mes' => $data['mes'] ?? null,
            'anio' => $data['anio'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comprobante registrado correctamente',
            'data' => $comprobante
        ], 201);
    }

    public function ObtenerRecibosPorCedula($cedula)
    {
        try {
            $recibos = ComprobantePago::where('cedula', $cedula)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($recibos, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener recibos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ActualizarEstadoRecibo(Request $request, $idPago)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,aceptado,rechazado'
            ]);


            $recibo = ComprobantePago::find($idPago);

            if (!$recibo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recibo no encontrado'
                ], 404);
            }

            $recibo->estado = $request->estado;
            $recibo->save();


            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'recibo' => $recibo
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

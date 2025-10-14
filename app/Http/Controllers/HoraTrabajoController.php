<?php

namespace App\Http\Controllers;

use App\Models\HoraTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoraTrabajoController extends Controller
{
    public function ObtenerHoras(Request $request)
    {
        $cedula = $request->attributes->get('cedula');
        if (!$cedula) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $horas = HoraTrabajo::where('cedula', $cedula)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($horas, 200);
    }

    public function InsertarHoras(Request $request)
    {
        $cedula = $request->attributes->get('cedula');
        if (!$cedula) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($request->has('horas') && !$request->has('conteo_de_horas')) {
            $request->merge(['conteo_de_horas' => intval($request->input('horas'))]);
        }

        $validator = Validator::make($request->all(), [
            'fecha' => ['required', 'date'],
            'conteo_de_horas' => ['required', 'integer', 'min:0', 'max:12'],
            'tipo_trabajo' => ['nullable', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
            'comprobante_compensacion' => ['nullable', 'string', 'max:255'],
            'monto_compensacion' => ['nullable', 'numeric', 'min:0'],
            'fecha_compensacion' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $tipoTrabajo = $data['tipo_trabajo'] ?? null;
        $query = HoraTrabajo::where('cedula', $cedula)
            ->whereDate('fecha', $data['fecha']);

        if ($tipoTrabajo) {
            $query->where('tipo_trabajo', $tipoTrabajo);
        } else {
            $query->where(function ($q) {
                $q->whereNull('tipo_trabajo')->orWhere('tipo_trabajo', '');
            });
        }

        $yaExiste = $query->exists();

        if ($yaExiste) {
            return response()->json(['message' => 'Ya registraste horas del mismo tipo para esa fecha.'], 409);
        }

        try {
            $registro = HoraTrabajo::create([
                'cedula' => $cedula,
                'fecha' => $data['fecha'],
                'conteo_de_horas' => $data['conteo_de_horas'],
                'tipo_trabajo' => $data['tipo_trabajo'] ?? null,
                'descripcion' => $data['descripcion'] ?? null,
                'comprobante_compensacion' => $data['comprobante_compensacion'] ?? null,
                'monto_compensacion' => $data['monto_compensacion'] ?? null,
                'fecha_compensacion' => $data['fecha_compensacion'] ?? null,
                'estado' => 'pendiente',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error creating record'], 500);
        }

        return response()->json($registro, 201);
    }
}

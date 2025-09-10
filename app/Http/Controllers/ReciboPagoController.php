<?php

namespace App\Http\Controllers;

use App\Models\ReciboPago;
use Illuminate\Http\Request;

class ReciboPagoController extends Controller
{
    // Listar todos los recibos
    public function index()
    {
        return ReciboPago::all();
    }

    // Crear un nuevo recibo
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'monto' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'estado' => 'required|string',
        ]);

        $recibo = ReciboPago::create($request->all());
        return response()->json($recibo, 201);
    }

    // Mostrar un recibo específico
    public function show($id)
    {
        return ReciboPago::findOrFail($id);
    }
}
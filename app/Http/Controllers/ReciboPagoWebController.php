<?php

namespace App\Http\Controllers;

use App\Models\ReciboPago;

class ReciboPagoWebController extends Controller
{
    public function index()
    {
        $recibos = ReciboPago::all();
        return view('recibo_pagos.index', compact('recibos'));
    }
}

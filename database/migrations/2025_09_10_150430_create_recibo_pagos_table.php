<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('recibo_pagos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('usuario_id');
        $table->decimal('monto', 10, 2);
        $table->date('fecha_pago');
        $table->string('estado')->default('pendiente');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibo_pagos');
    }
};

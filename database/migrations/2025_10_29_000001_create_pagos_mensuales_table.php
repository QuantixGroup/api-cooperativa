<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosMensualesTable extends Migration
{
    public function up()
    {
        Schema::create('pagos_mensuales', function (Blueprint $table) {
            $table->bigIncrements('id_pago');
            $table->unsignedInteger('cedula');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_comprobante');
            $table->string('archivo_comprobante')->nullable();
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente');
            $table->integer('mes')->nullable();
            $table->integer('anio')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->index(['mes', 'anio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos_mensuales');
    }
}

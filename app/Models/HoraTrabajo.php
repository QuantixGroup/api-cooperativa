<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoraTrabajo extends Model
{
    protected $table = 'registros_horas';

    protected $fillable = [
        'cedula',
        'fecha',
        'conteo_de_horas',
        'tipo_trabajo',
        'descripcion',
        'comprobante_compensacion',
        'monto_compensacion',
        'fecha_compensacion',
        'estado',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

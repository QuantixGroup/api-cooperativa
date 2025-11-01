<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprobantePago extends Model
{
    protected $table = 'pagos_mensuales';

    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'cedula',
        'monto',
        'fecha_comprobante',
        'archivo_comprobante',
        'estado',
        'mes',
        'anio',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_comprobante' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

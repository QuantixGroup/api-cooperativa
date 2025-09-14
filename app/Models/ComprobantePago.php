<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprobantePago extends Model
{
    protected $table = 'pagos_mensuales';

    public function usuario(): BelongsTo {
    return $this->belongsTo(User::class, 'user_id');
}
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendientesProduccion extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_PendientesProduccionV2';

    /**
     * @var string[]
     */
    protected $fillable = [
        'ITEM', 'OP', 'OV', 'REFERENCIA', 'COD_PROD', 'PRODUCTO', 'ACABADO', 'LOTE', 'ARTE', 'MARCA', 'SECUENCIA', 'PESO',
        'CT', 'CT_PRE', 'OPERACION', 'CANT_COMPLETADA', 'CANT_PENDIENTE', 'FECHA_LIBERACION', 'FECHA_MOV', 'FECHA_ANT_MOV','DIAS_OV','DIAS_CT'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'days',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'FECHA_LIBERACION' => 'date:Y-m-d',
        'FECHA_MOV' => 'date:Y-m-d',
        'FECHA_ANT_MOV' => 'date:Y-m-d',
        'CANT_COMPLETADA' => 'int',
        'CANT_PENDIENTE' => 'int',
        'PESO' => 'float',
        'DIAS_OV'=> 'int',
        'DIAS_CT'=> 'int',
    ];

    /**
     * @return int
     */
    public function getDaysAttribute(): int
    {
        return Carbon::now()->diffInDays($this->FECHA_ANT_MOV);
    }
}

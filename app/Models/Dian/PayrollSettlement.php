<?php

namespace App\Models\Dian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollSettlement extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'IDENTIFICACION', 'EMPLEADO', 'AÑO', 'mes', 'periodo', 'INICIO', 'FIN', 'centro', 'DESCRIPCION_CENTRO',
        'CODIGO_BANCO', 'Banco', 'CUENTA', 'CONCEPTO', 'DESCRIPCION_CONCEPTO', 'HORAS', 'valor', 'planta', 'oficio',
        'contrato', 'DEVENGADO', 'PAGO', 'DEDUCCIONES',
    ];

    /**
     * @var string
     */
    protected $connection = 'DMS';

    /**
     * @var string
     */
    protected $table = 'V_CIEV_Liquidaciones';

    /**
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PayrollDocument::class, 'employee_id', 'IDENTIFICACION')
            ->whereIn('type_document_id', [9, 10])
            ->where('state_document_id', '=', 1);
    }
}

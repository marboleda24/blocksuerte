<?php

namespace App\Models;

use App\Models\Dian\ApiDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\MockObject\Api;

class MAXInvoice extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_FE_FacturasTotalizadas';

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var string
     */
    protected $primaryKey = 'NUMERO';

    /**
     * @var string[]
     */
    protected $appends = ['taxable', 'state'];

    /**
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(MAXInvoiceDetail::class, 'Factura', 'NUMERO');
    }

    /**
     * @return bool
     */
    public function getTaxableAttribute(): bool
    {
        if ($this->CODIVA === 'IVA-V19'){
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getStateAttribute(): bool
    {
        $exist = ApiDocument::where('state_document_id', '=', 1)
            ->where('number', '=', $this->NUMERO)
            ->count();

        return $exist > 0;
    }
}

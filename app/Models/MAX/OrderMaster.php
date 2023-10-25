<?php

namespace App\Models\MAX;

use App\Models\VisualControl\ProductionBatch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderMaster extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_OP';

    /**
     * @return HasOne
     */
    public function production_batch(): HasOne
    {
        return $this->hasOne(ProductionBatch::class, 'Lote', 'ORDNUM_10');
    }
}

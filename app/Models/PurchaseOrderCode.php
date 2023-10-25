<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderCode extends Model
{
    use HasFactory;
    use Compoships;

    /**
     * @var string
     */
    protected $primaryKey = 'ORDNUM_16';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'Purchase_Order_Code';

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(RawMaterial::class, 'oc', trim($this->ORDNUM_16).'0101');
    }

    /**
     * @return HasOne
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(VendorMaster::class, 'VENID_08', 'VENID_16');
    }
}

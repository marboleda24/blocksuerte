<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'ORDNUM_10';

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
    protected $table = 'Order_Master';

    public function item()
    {
        return $this->hasOne(PartMaster::class, 'PRTNUM_01', 'PRTNUM_10');
    }

    public function vendor()
    {
        return $this->hasOne(VendorMaster::class, 'VENID_08', '');
    }
}

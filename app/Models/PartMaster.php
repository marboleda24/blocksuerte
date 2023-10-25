<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PartMaster extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'PRTNUM_01';

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
    protected $table = 'Part_Master';

    /**
     * @return HasOne
     */
    public function part_sales(): HasOne
    {
        return $this->hasOne(PartSales::class, 'PRTNUM_29', 'PRTNUM_01');
    }
}

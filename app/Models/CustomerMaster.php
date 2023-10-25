<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMaster extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'Customer_Master';

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'CUSTID_23';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';
}

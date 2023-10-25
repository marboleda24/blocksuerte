<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityDMS extends Model
{
    use HasFactory;
    use Compoships;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'DMS';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'y_ciudades';

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'ciudad';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryDMS extends Model
{
    use HasFactory;

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
    protected $table = 'y_paises';

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'pais';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';
}

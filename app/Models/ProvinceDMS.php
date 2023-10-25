<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvinceDMS extends Model
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
    protected $table = 'y_departamentos';

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'departamento';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileOrder extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'path', 'master_id',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files_orders';
}

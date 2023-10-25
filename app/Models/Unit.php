<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    /**
     * primaryKey
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * keyType
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * disabled since the primary key is a string
     *
     * @var undefined
     */
    public $incrementing = false;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name', 'comments', 'created_by', 'state',
    ];

    /**
     * subline
     *
     * @return void
     */
    public function subline()
    {
        return $this->belongsTo(SubLine::class);
    }
}

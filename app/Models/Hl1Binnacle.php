<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Hl1Binnacle extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'start_time', 'end_time', 'operator_id', 'date',
        'maintenance', 'ingots', 'filter_pressure', 'observations', 'created_by',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hl1_binnacle';

    /**
     * Machine
     *
     * @return HasOne
     */
    public function Machine(): HasOne
    {
        return $this->hasOne(MachineBinnacle::class, 'id', 'machine_id');
    }

    /**
     * Operator
     *
     * @return HasOne
     */
    public function Operator(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * CreatedBy
     *
     * @return HasOne
     */
    public function CreatedBy(): HasOne
    {
        return $this->hasOne(User::class);
    }
}

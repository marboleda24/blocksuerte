<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Px00Binnacle extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'date', 'workshift', 'tb', 'rz', 'vz', 'z',
        'operator_id', 'maintenance', 'type_maintenance', 'observations',
        'maintenance_operator_id', 'created_by',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'px00_binnacle';

    /**
     * Machine
     *
     * @return void
     */
    public function Machine()
    {
        return $this->hasOne(MachineBinnacle::class, 'id', 'machine_id');
    }

    /**
     * Operator
     *
     * @return void
     */
    public function Operator()
    {
        return $this->hasOne(User::class);
    }

    /**
     * CreatedBy
     *
     * @return void
     */
    public function CreatedBy()
    {
        return $this->hasOne(User::class);
    }
}

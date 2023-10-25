<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;
    use Compoships;

    /**
     * @var string
     */
    protected $connection = 'evpiu';

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'oc', 'entry', 'entry_id', 'dimension', 'appearance', 'weight', 'observation', 'received_by', 'created_by',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['created_by'];

    public function receivedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'received_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

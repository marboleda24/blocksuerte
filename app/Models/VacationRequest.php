<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VacationRequest extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'employee_document', 'start_date', 'end_date', 'justify', 'boss_document',
        'boss_approved_date', 'state', 'approved_human_resource', 'observations',
    ];

    /**
     * @var string[]
     */
    public $timestamps = false;

    /**
     * @return HasOne
     */
    public function boss(): HasOne
    {
        return $this->hasOne(V_CIEV_Personal::class, 'nit', 'boss_document');
    }

    /**
     * @return HasOne
     */
    public function approved_rrhh(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_human_resource');
    }

    /**
     * @return HasOne
     */
    public function employee(): HasOne
    {
        return $this->hasOne(V_CIEV_Personal::class, 'nit', 'employee_document');
    }
}

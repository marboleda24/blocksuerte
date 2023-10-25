<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobLetterRequest extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'employee_document', 'addressed_to', 'approved_by', 'approved_date', 'state', 'observations',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'approved_by',
    ];

    /**
     * @return HasOne
     */
    public function approvedby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    /**
     * @return HasOne
     */
    public function employee(): HasOne
    {
        return $this->hasOne(V_CIEV_Personal::class, 'nit', 'employee_document');
    }
}

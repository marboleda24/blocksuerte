<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_document', 'temperature', 'fever', 'cough', 'throat_pain', 'respiratory_distress', 'loss_of_taste', 'contact_infected_person', 'observations', 'time_of_entry', 'created_by',

    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(V_CIEV_Personal::class, 'nit', 'employee_document');
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

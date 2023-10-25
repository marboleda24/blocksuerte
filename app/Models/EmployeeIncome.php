<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeIncome extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'document', 'fist_name', 'second_name', 'first_lastname', 'second_lastname',
        'gender', 'birthday', 'blood_type', 'entry_datetime', 'exit_datetime', 'type',
    ];
}

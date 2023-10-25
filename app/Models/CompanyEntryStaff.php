<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyEntryStaff extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['document', 'entry', 'exit'];

    public $timestamps = ['entry', 'exit'];
}

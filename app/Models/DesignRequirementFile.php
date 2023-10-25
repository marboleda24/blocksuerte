<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignRequirementFile extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['design_requirement_id', 'name', 'path', 'type'];

    protected $hidden = ['design_requirement_id'];
}

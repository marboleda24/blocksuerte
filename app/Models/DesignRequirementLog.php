<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DesignRequirementLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['design_requirement_id', 'description', 'created_by', 'type'];

    /**
     * @var string[]
     */
    protected $hidden = ['design_requirement_id', 'created_by'];

    /**
     * @return HasOne
     */
    public function created_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

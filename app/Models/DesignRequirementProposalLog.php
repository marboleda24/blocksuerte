<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DesignRequirementProposalLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['proposal_id', 'description', 'created_by', 'type'];

    /**
     * @var string[]
     */
    protected $hidden = ['proposal_id', 'created_by'];

    /**
     * @return HasOne
     */
    public function created_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

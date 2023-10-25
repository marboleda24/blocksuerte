<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DesignRequirementArt extends Model
{
    use HasFactory, Compoships;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'design_requirement_id', 'proposal_id', 'code', 'archaic_product_code'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'design_requirement_id', 'proposal_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'current'
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'product'
    ];

    /**
     * @return HasOne
     */
    public function design_requirement(): HasOne
    {
        return $this->hasOne(DesignRequirementHeader::class, 'id', 'design_requirement_id');
    }

    /**
     * @return HasOne
     */
    public function proposal(): HasOne
    {
        return $this->hasOne(DesignRequirementProposal::class, 'id', 'proposal_id');
    }

    /**
     * @return HasMany
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DesignRequirementArtVersion::class, 'art_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(DesignRequirementProduct::class, 'art_code', 'code');
    }

    /**
     * @return mixed
     */
    public function getCurrentAttribute(): mixed
    {
        $current = $this->versions->where('enabled', '=', true)->first();
        $current?->seller();

        return $current;
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyEntryInvited extends Model
{
    use HasFactory;

    protected $table = 'company_entry_invited';

    /**
     * @var string[]
     */
    protected $fillable = ['document', 'name', 'sex', 'birth', 'blood_type'];

    /**
     * @return HasMany
     */
    public function registry(): HasMany
    {
        return $this->hasMany(CompanyEntryInvitedRegistry::class, 'document', 'document');
    }
}

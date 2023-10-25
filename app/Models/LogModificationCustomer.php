<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogModificationCustomer extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['field', 'justify', 'modified_by', 'customer_code'];

    /**
     * @var string[]
     */
    protected $hidden = ['modified_by'];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'modified_by');
    }
}

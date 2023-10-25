<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BlueprintFile extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['blueprint_id', 'path', 'miniature', 'upload_by', 'version', 'state', 'details', 'type'];

    /**
     * @var string[]
     */
    protected $hidden = ['blueprint_id', 'upload_by'];

    /**
     * @return HasOne
     */
    public function upload_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

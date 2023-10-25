<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemoteAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'url', 'icon', 'state', 'created_by',
    ];

    public $table = 'remote_access';
}

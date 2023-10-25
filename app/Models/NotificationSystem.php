<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSystem extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['application', 'state', 'modified_by'];
}

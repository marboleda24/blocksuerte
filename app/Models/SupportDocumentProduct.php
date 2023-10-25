<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportDocumentProduct extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['description', 'created_id'];

    /**
     * @var string[]
     */
    protected $hidden = ['type_item_identification', 'created_id'];
}

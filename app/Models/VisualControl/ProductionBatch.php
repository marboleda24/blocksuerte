<?php

namespace App\Models\VisualControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'VISUAL_CONTROL';

    /**
     * @var string
     */
    protected $table = 'LotesProduccion';
}

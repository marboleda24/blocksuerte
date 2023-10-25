<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'evpiu';

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_code', 'path',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'name',
    ];

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        return substr($this->path, strrpos($this->path, '/') + 1);
    }
}

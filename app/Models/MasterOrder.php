<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOrder extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ['cellar', 'production', 'dies'];

    /**
     * production
     *
     * @return void
     */
    public function production()
    {
        return $this->hasOne(HeaderOrder::class, 'id', 'production');
    }

    /**
     * cellar order
     *
     * @return void
     */
    public function cellar()
    {
        return $this->hasOne(HeaderOrder::class, 'id', 'cellar');
    }

    /**
     * dies
     *
     * @return void
     */
    public function dies()
    {
        return $this->hasOne(HeaderOrder::class, 'id', 'dies');
    }

    /**
     * get files to upload
     *
     * @return void
     */
    public function files()
    {
        return $this->hasMany(FileOrder::class, 'master_id', 'id');
    }
}

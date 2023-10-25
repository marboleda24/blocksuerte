<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogOrder extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'description', 'type', 'work_center', 'created_by',
    ];

    /**
     * seller
     *
     * @return void
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function tickets()
    {
        return $this->belongsToMany(Event::class, 'nombre_tabla_pivote')
            ->withPivot('event_id', 'client_id');
    }

    public function test()
    {
        $events = Event::with('tickets')->find(id);
        $cantidad = $events->tickets->count();
    }
}

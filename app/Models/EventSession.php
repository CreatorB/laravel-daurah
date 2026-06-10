<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSession extends Model
{
    protected $table = 'event_sessions';
    
    public $timestamps = false;

    protected $fillable = ['event_id', 'nama_sesi', 'jam_mulai', 'jam_selesai', 'material_id'];

    protected $hidden = [''];

    protected $casts = [];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

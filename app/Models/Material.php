<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    
    public $timestamps = false;

    protected $fillable = ['event_id', 'nama_materi', 'deskripsi'];

    protected $hidden = [''];

    protected $casts = [];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

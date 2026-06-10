<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    
    public $timestamps = false;

    protected $fillable = ['user_id', 'event_id', 'session_id', 'waktu_scan', 'materi_confirmed', 'materi_confirmed_at'];

    protected $hidden = [''];

    protected $casts = [
        'waktu_scan' => 'datetime', 'materi_confirmed' => 'boolean', 'materi_confirmed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function session()
    {
        return $this->belongsTo(EventSession::class, 'session_id');
    }
}

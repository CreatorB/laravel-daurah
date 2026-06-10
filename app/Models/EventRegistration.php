<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $table = 'event_registrations';
    
    public $timestamps = false;

    protected $fillable = ['event_id', 'user_id', 'status'];

    protected $hidden = [''];

    protected $casts = [
        'status' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

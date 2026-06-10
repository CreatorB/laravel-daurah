<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    protected $table = 'qr_tokens';
    
    public $timestamps = false;
    
    protected $fillable = ['token', 'event_id', 'expires_at'];
    
    protected $hidden = [''];
    
    protected $casts = [
        'expires_at' => 'datetime'
    ];
}

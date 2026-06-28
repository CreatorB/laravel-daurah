<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    
    public $timestamps = false;

    protected $fillable = ['nama_event', 'tanggal', 'qr_mode', 'cert_enabled', 'cert_template', 'cert_font', 'cert_font_size', 'cert_font_color', 'radius_lat', 'radius_lng', 'radius_active', 'radius_meters', 'group_link', 'material_type', 'auto_confirm', 'auto_invite', 'konfirmasi_buka', 'konfirmasi_tutup'];

    protected $hidden = [''];

    protected $casts = [
        'radius_active' => 'boolean', 'auto_confirm' => 'boolean', 'auto_invite' => 'boolean', 'cert_enabled' => 'boolean', 'radius_lat' => 'decimal:8', 'radius_lng' => 'decimal:8', 'radius_meters' => 'integer'
    ];

    public function sessions()
    {
        return $this->hasMany(EventSession::class, 'event_id')->orderBy('jam_mulai');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'event_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'event_id');
    }
}

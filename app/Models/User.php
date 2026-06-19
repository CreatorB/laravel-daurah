<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = ['nama', 'nohp', 'email', 'password', 'alamat', 'lembaga', 'role', 'domisili', 'menginap', 'agreement_accepted_at', 'bukti_undangan'];

    protected $hidden = ['password'];

    protected $casts = [
        'agreement_accepted_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function setNohpAttribute($value)
    {
        $phone = preg_replace('/[^0-9]/', '', $value);

        if (substr($phone, 0, 3) === '620') {
            $this->attributes['nohp'] = '0' . substr($phone, 3);
        } elseif (substr($phone, 0, 2) === '62') {
            $this->attributes['nohp'] = '0' . substr($phone, 2);
        } elseif (substr($phone, 0, 1) === '0') {
            $this->attributes['nohp'] = $phone;
        } else {
            $this->attributes['nohp'] = '0' . $phone;
        }
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}

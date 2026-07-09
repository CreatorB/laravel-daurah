<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleBin extends Model
{
    protected $table = 'recycle_bin';

    public $timestamps = false;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'label',
        'snapshot',
        'deleted_by_name',
        'deleted_at',
        'restored_at',
        'restored_by_name',
        'permanently_deleted_at',
        'permanently_deleted_by_name',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'deleted_at' => 'datetime',
        'restored_at' => 'datetime',
        'permanently_deleted_at' => 'datetime',
    ];

    public function entity()
    {
        if (!class_exists($this->entity_type)) {
            return null;
        }
        return $this->entity_type::withTrashed()->find($this->entity_id);
    }
}

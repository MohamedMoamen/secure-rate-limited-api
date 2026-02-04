<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
   protected $fillable = [
        'user_id',
        'action',
        'endpoint',
        'ip_address',
        'task_id',
        'old_data',
        'new_data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notify_time_id'
    ];
}

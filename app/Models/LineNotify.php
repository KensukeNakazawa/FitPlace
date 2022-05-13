<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineNotify extends Model
{
    use HasFactory;

    protected $table = 'line_notifies';

    protected $fillable = [
        'user_id',
        'access_token',
        'check_at'
    ];
}

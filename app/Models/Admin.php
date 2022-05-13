<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'login_id',
        'password',
        'name',
        'authority',
        'created_at',
        'updated_at'
    ];

    /**
     * 配列に対して、非表示にする必要のある属性
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'auth_id',
        'birth_day',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $dates = [
        'birth_day'
    ];

    /**
     * アクセストークンを取得する
     * @return string $access_token
     */
    public function getAccessToken()
    {
        return $this->lineNotify->access_token;
    }

    public function exerciseTypes()
    {
        return $this->hasMany('App\Models\ExerciseType');
    }

    public function auth()
    {
        return $this->belongsTo('App\Models\Auth');
    }

    public function lineNotify()
    {
        return $this->hasOne('App\Models\LineNotify');
    }
}

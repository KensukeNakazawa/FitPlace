<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Auth extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'twitter_token',
        'twitter_token_secret',
        'google_auth_key',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


}

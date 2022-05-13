<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public function show($user_id)
    {
        $user = User::find($user_id);
        return view('user.show', ['user' => $user]);
    }
}

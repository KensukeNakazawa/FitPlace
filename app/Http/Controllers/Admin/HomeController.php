<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $users = User::paginate(config('constants.PAGINATION'));
        return view('home.home', ['users' => $users]);
    }
}

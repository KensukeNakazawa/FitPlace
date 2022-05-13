<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function loginShow()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $login_id = $request->login_id;
        $password = $request->password;

        $admin = Admin::where('login_id', $login_id)->get()->first();

        try {
            if ($admin === null || !Hash::check($password, $admin->password)) {
                throw new \Exception('ログインに失敗しました。');
            }
            $request->session()->put('login_admin_id', $admin->id);
        } catch (\Exception $error) {
            return redirect()->back()->withErrors($error->getMessage());
        }
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('login_admin_id');
        return redirect()->route('auth.loginShow');
    }
}

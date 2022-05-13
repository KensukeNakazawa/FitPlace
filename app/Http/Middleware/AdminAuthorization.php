<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;
use Closure;

use App\Models\Admin;

class AdminAuthorization
{

    public function __construct()
    {

    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $login_admin_id = $request->session()->get('login_admin_id');

        $admin = Admin::where('id', $login_admin_id)->get();

        if ($login_admin_id === null || $admin->isEmpty() ) {
            return redirect()->route('auth.loginShow')->withErrors('ログインしてください');
        }

        return $next($request);
    }
}

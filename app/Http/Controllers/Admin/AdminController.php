<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::paginate(config('constants.PAGINATION'));

        return view('admin.index', ['admins' => $admins]);
    }

    public function create(Request $request)
    {
        $login_admin_id = $request->session()->get('login_admin_id');

        $admin = Admin::find($login_admin_id);
        return view('admin.create', ['admin' => $admin]);
    }

    public function store(Request $request)
    {
        $admin = Admin::create($request->all());

        $admin->fill(['password' => Hash::make($request->password)])->save();

        return redirect()->route('admin.index');
    }

    public function destroy($admin_id)
    {
        Admin::destroy($admin_id);

        return redirect()->route('admin.index');
    }


}

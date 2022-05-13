<?php

namespace App\Http\Controllers;

use App\Lib\Cache\RedisImplement;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Auth;
use Illuminate\Support\Facades\Crypt;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function getAuth(Request $request)
    {
        $request_authorization = explode(':', $request->header('Authorization'));
        $request_token = trim($request_authorization[1]);

        $auth = $this->getAuthFromToken($request_token);

        return $auth;
    }

    protected function getAuthFromToken($request_token)
    {
        $redis = new RedisImplement();

        $encrypted_login_id = $redis->getValue($request_token);
        $decrypted_login_id = Crypt::decrypt($encrypted_login_id);

        $key_vale =  explode(':', $decrypted_login_id);
        $auth_type = $key_vale[0];
        $login_id = $key_vale[1];

        if ($auth_type === config('enum.AUTH_TYPE.EMAIL')) {
            $auth = Auth::where('email', $login_id)->first();
        } elseif ($auth_type === config('enum.AUTH_TYPE.TWITTER')) {
            $auth = Auth::where('twitter_token', $login_id)->first();
        }

        return $auth;
    }
}

<?php

namespace App\Http\Controllers\ExternalApi;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Controller;

use App\Models\Auth;

use App\Services\AuthService;


class TwitterAuthController extends Controller
{

    private AuthService $authService;

    /**
     * TwitterAuthController constructor.
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * Twitterの認証ページヘユーザーをリダイレクト
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Twitterからユーザー情報を取得(Callback先)
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function handleCallback()
    {
        $twitter_user = Socialite::driver('twitter')->user();
        $twitter_user->token;

        # 登録済みユーザーで新たに連携
        if (session()->has('twitter_connect_auth_id')) {
            $auth_id = session()->get('twitter_connect_auth_id');
            $token = $this->authService->connectTwitter($auth_id, $twitter_user);
            session()->forget('twitter_connect_auth_id');
            return redirect('/my_page/home');
        } else {
            # ログインまたは会員登録
            $auth = Auth::where('twitter_token', $twitter_user->token)->first();
            if (!$auth) {
                $token = $this->authService->registerFromTwitter($twitter_user);
                return redirect('/users/create')->with('token', $token);
            }
            $token = $this->authService->authorizeTwitter($auth->twitter_token);
            return redirect('/home')->with('token', $token);
        }

    }

    public function connectTwitter(Request $request)
    {
        $auth = $this->getAuthFromToken($request->token);
        session()->put('twitter_connect_auth_id', $auth->id);
        return Socialite::driver('twitter')->redirect();
    }

    public function testCallback()
    {
        $token_twitter = 'test_token';
        return redirect('/home')->with('token', $token_twitter);
    }
}

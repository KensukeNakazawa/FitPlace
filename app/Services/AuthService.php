<?php

namespace App\Services;

use App\Mail\SampleNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Exceptions\AuthenticateFailedException;

use App\Lib\Cache\RedisInterface;
use App\Lib\Transaction\TransactionInterface;

use App\Exceptions\RateLimitException;

use App\Models\User;
use App\Models\Auth;

/** ここあとで抽象化する */

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;


class AuthService extends BaseService
{
    /** ログインのレートリミットの回数制限は10  */
    const RATE_LIMIT_MAX = 10;

    private RedisInterface $redis;
    private TransactionInterface $transaction;
    private User $userModel;
    private Auth $authModel;

    /**
     * AuthService constructor.
     * @param RedisInterface $redis
     * @param TransactionInterface $transaction
     * @param User $userModel
     * @param Auth $authModel
     */
    public function __construct(
        RedisInterface $redis,
        TransactionInterface $transaction,
        User $userModel,
        Auth $authModel
    ) {
        $this->redis = $redis;
        $this->transaction = $transaction;
        $this->userModel = $userModel;
        $this->authModel = $authModel;
    }


    /**
     * @param string $name
     * @param string $email
     * @param string $password
     */
    final public function registerUser(string $email, string $password)
    {
        $this->transaction->begin();
        try {
            $auth = $this->authModel->newQuery()->where(['email' => $email])->first();

            if ($auth === null) {
                $this->authModel->newQuery()->create(
                    [
                        'email' => $email,
                        'password' => Hash::make($password)
                    ]
                );
            } elseif ($this->userModel->newQuery()->where('auth_id', $auth->id)->get()->isNotEmpty()) {
                /**
                 * ユーザーが既にいる場合はなにもしない
                 * セキュリティ的に存在を確定させてしまうとまずいので、エラーは出さない
                 */
                return Null;
            }

            $auth_code = rand(10000, 99999);

            $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $email;
            $this->redis->setKeyValue($key, $auth_code, config('enum.REDIS_TTL.AUTH_CODE'));

            $this->sendEmail($email, $auth_code);

            $this->transaction->commit();
        } catch (QueryException $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(409, $this->trans_message('messages.register_failed'));
        }
    }

    /**
     * Twitterからのユーザーを登録する
     * @param $twitter_user
     * @return string
     */
    final public function registerFromTwitter($twitter_user)
    {
        $auth = $this->authModel->newQuery()->create(
            ['twitter_token' => $twitter_user->token, 'twitter_token_secret' => $twitter_user->tokenSecret]
        );
        return $this->setAuthToken($auth, config('enum.AUTH_TYPE.TWITTER'));
    }

    /**
     * 既に登録済みのユーザーをTwitter連携する
     * @param $auth_id
     * @param $twitter_user
     * @return string
     */
    final public function connectTwitter($auth_id, $twitter_user)
    {
        $auth = $this->authModel->newQuery()->find($auth_id);
        $auth->update(
            [
                'twitter_token' => $twitter_user->token,
                'twitter_token_secret' => $twitter_user->tokenSecret
            ]
        );
        return $this->setAuthToken($auth, config('enum.AUTH_TYPE.TWITTER'));
    }

    final public function authorizeCode(string $auth_id, string $auth_code)
    {
        $key = config('enum.CACHE_KEY_PREFIX.SIGNUP_AUTH_ID') . $auth_id;

        /** 認証コードが正しければ、正常処理 */
        try {
            if ($auth_code !== $this->redis->getValue($key)) {
                throw new AuthenticateFailedException($this->trans_message('messages.auth_code_failed'));
            }
        } catch (AuthenticateFailedException $error) {
            Log::error($error->getMessage());
            abort(403, $error->getMessage());
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            abort(400, $error->getMessage());
        }

        $auth = $this->authModel->newQuery()->where(['email' => $auth_id])->first();

        return $this->setAuthToken($auth, config('enum.AUTH_TYPE.EMAIL'));
    }

    /**
     * @param string $email
     * @param string $password
     * @return string $token
     * @throws \Exception
     */
    final public function authorizeEmail(string $email, string $password)
    {
        $this->rateLimit($email);

        $auth = $this->authModel->newQuery()->where(['email' => $email])->first();

        try {
            if ($auth === null || !Hash::check($password, $auth->password)) {
                throw new AuthenticateFailedException($this->trans_message('messages.login_failed'));
            }

        } catch (AuthenticateFailedException $error) {
            Log::error($error->getMessage());
            abort(403, $error->getMessage());
        }

        return $this->setAuthToken($auth, config('enum.AUTH_TYPE.EMAIL'));
    }

    /**
     * @param string $twitter_token
     * @return string $token
     * @throws \Exception
     */
    final public function authorizeTwitter(string $twitter_token)
    {
        $auth = $this->authModel->newQuery()->where(['twitter_token' => $twitter_token])->first();

        if ($auth === null) {
            throw new \Exception("エラーだよ");
        }

        return $this->setAuthToken($auth, config('enum.AUTH_TYPE.TWITTER'));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Auth $auth
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    final public function getAuthUser(Auth $auth): User
    {
        $user = $this->userModel->newQuery()->where(['auth_id' => $auth->id])->first();
        return $user;
    }

    /**
     * @param User $user
     * @param string $old_password
     * @param string $new_password
     */
    final public function changePassword(User $user, string $old_password, string $new_password) : void
    {
        $auth = $user->auth;

        try {
            if ($auth === null || !Hash::check($old_password, $auth->password)) {
                throw new \Exception($this->trans_message('messages.password_change_failed'));
            }

            $auth->update(
                [
                    'password' => Hash::make($new_password)
                ]
            );
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            abort(500, $error->getMessage());
        }

    }


    /**
     * メールを送信する
     * @param string $email
     * @param int $auth_code
     */
    final private function sendEmail(string $email, int $auth_code)
    {
        $mail_text = "FitPlaceへようこそ！\n登録ありがとうございます。\n\n";
        $mail_text .= "認証コードは${auth_code}です。\n\n";
        $mail_text .= "https://fit-place.me/auth/auth_code より入力してください。\n";
        $mail_text .= "認証コードの有効期限は2時間です。\n\n\n";

        $mail_text .= "-----------------------------------------------\n";
        $mail_text .= "FitPlace\n";
        $mail_text .= "Email: inform.fitplace@gmail.com\n";
        $mail_text .= "https://fit-place.me/\n";
        $mail_text .= "-----------------------------------------------\n";

        $title = 'FitPlace 会員登録認証コードの送信';
        Mail::to($email)->send(new SampleNotification($title, $mail_text));
    }

    /**
     * @param $auth
     * @return string
     */
    final private function setAuthToken($auth, string $login_type)
    {
        if ($login_type === config('enum.AUTH_TYPE.EMAIL')) {

            $original_string = env('JWT_SECRET') . $auth->email . uniqid();
            $key_token = hash('sha256', $original_string);
            $key_value = Crypt::encrypt(config('enum.AUTH_TYPE.EMAIL') . ':' .  $auth->email);
        } elseif ($login_type === config('enum.AUTH_TYPE.TWITTER')) {

            $original_string = env('JWT_SECRET') . $auth->twitter_token . uniqid();
            $key_token = hash('sha256', $original_string);
            $key_value = Crypt::encrypt(config('enum.AUTH_TYPE.TWITTER') . ':' .  $auth->twitter_token);
        }

        $this->redis->setKeyValue($key_token, $key_value, config('enum.REDIS_TTL.AUTH_TOKEN'));

        return $key_token;
    }

    /**
     * 対象のログインIDに対して、１０回失敗したら１０分間の制限をかける
     * @param $auth_id
     */
    final private function rateLimit($auth_id)
    {
        $cache_key = config('enum.CACHE_KEY_PREFIX.AUTH_USER_RATE_LIMIT') . $auth_id;
        $ttl = config('enum.REDIS_TTL.LOGIN_RATE_LIMIT');

        $value = $this->redis->getValue($cache_key);
        if (empty($value)) {
            $this->redis->setKeyValue($cache_key, 0, $ttl);
        }

        try {
            if ($value >= self::RATE_LIMIT_MAX) {
                throw new RateLimitException('ログイン制限回数に達したため、しばらくの間ログインできません');
            }
        } catch (RateLimitException $error) {
            Log::error($error->getMessage());
            abort(423, $error->getMessage());
        }

        $this->redis->countUp($cache_key, $ttl);

    }
}

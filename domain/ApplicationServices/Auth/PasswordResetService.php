<?php

namespace Domain\ApplicationServices\Auth;

use Illuminate\Support\Facades\Hash;

use App\Mail\PasswordReset;
use App\Lib\Cache\RedisInterface;
use App\Models\Auth;
use Illuminate\Support\Facades\Mail;


class PasswordResetService
{
    private RedisInterface $redisInterface;
    private Auth $authModel;

    /**
     * PasswordResetService constructor.
     * @param RedisInterface $redisInterface
     * @param Auth $authModel
     */
    public function __construct(RedisInterface $redisInterface, Auth $authModel)
    {
        $this->redisInterface = $redisInterface;
        $this->authModel = $authModel;
    }


    /**
     * パスワード再設定用のメールを送信する
     * @param string $email
     */
    public function sendMail(string $email)
    {
        $auths = $this->authModel->newQuery()->where('email', $email)->get();
        if ($auths->isNotEmpty()) {
            $key = $this->generateToken($email);
            $this->sendMailForResetURL($email, $key);
        }

    }

    /**
     * パスワードを更新する
     * - 入力されたトークンを元にRedisからauthのemailを取得する
     * - 対象のauth.passwordを更新する
     * - パスワード再設定完了のメールを送信する
     * @param string $auth_code
     * @param string $new_password
     */
    public function resetPassword(string $auth_code, string $new_password)
    {
        $email = $this->getEmailFromToken($auth_code);
        if (empty($email)) {
            abort(404, 'コードがありません');
        }
        $auth = $this->authModel->newQuery()->where('email', $email)->first();
        $auth->update(
            [
                'password' => Hash::make($new_password)
            ]
        );
        $this->sendResetCompleteMail($email);
    }

    /**
     * パスワード再設定用のトークンを生成し、Redisに保存する
     * メールで送信するのは、RedisのキーをBase64でエンコードしたもの
     * @param $email
     * @return string
     */
    private function generateToken($email): string
    {
        $key = uniqid($email, true);
        $this->redisInterface->setKeyValue($key, $email, config('enum.REDIS_TTL.PASSWORD_RESET'));
        return base64_encode($key);
    }

    /**
     * 入力されたトークンを元にredisからemailを取得する
     * @param $auth_code
     * @return string|null
     */
    private function getEmailFromToken($auth_code)
    {
        $decoded_key = base64_decode($auth_code);
        $email = $this->redisInterface->getValue($decoded_key);
        $this->redisInterface->deleteValue($decoded_key);
        return $email;
    }

    /**
     * パスワード再設定用のURLを送信するメールの送信
     * @param string $email
     * @param string $auth_code
     */
    private function sendMailForResetURL(string $email, string $auth_code): void
    {
        $mail_text = "パスワード再設定をご案内します\n以下のURLにアクセスしてパスワードを再設定してください。\n\n";
        $mail_text .= "https://fit-place.me/auth/password_reset/reset?auth_code=${auth_code}\n";
        $mail_text .= "URLの期限は20分です。\n\n\n";

        $mail_text .= "-----------------------------------------------\n";
        $mail_text .= "FitPlace\n";
        $mail_text .= "Email: inform.fitplace@gmail.com\n";
        $mail_text .= "https://fit-place.me/\n";
        $mail_text .= "-----------------------------------------------\n";

        $title = '【FitPlace】パスワード再設定のご案内';
        Mail::to($email)->send(new PasswordReset($title, $mail_text));
    }

    /**
     * パスワードの再設定が終了したことを知らせるメールを送信する
     * @param $email
     */
    private function sendResetCompleteMail($email)
    {
        $mail_text = "パスワード再設定が完了しました。\n以下のURLからログインしてください。\n\n";
        $mail_text .= "https://fit-place.me/auth/login\n";

        $mail_text .= "-----------------------------------------------\n";
        $mail_text .= "FitPlace\n";
        $mail_text .= "Email: inform.fitplace@gmail.com\n";
        $mail_text .= "https://fit-place.me/\n";
        $mail_text .= "-----------------------------------------------\n";

        $title = '【FitPlace】パスワード再設定完了のご連絡';
        Mail::to($email)->send(new PasswordReset($title, $mail_text));
    }
}
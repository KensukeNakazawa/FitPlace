<?php

return [
    'USER_STATUS' => [
        'UNAUTHENTICATED' => 1,
        'AUTHENTICATED' => 2,
        'DELETED' => 3
    ],

    'CACHE_KEY_PREFIX' => [
        'SIGNUP_AUTH_ID' => 'signup_auth_id_',
        'AUTHENTICATED_ID' => 'authenticated_id',
        /** ログインしているユーザーの種目 */
        'AUTH_USER_EXERCISE_TYPE' => 'auth_user_exercise_type_',
        /** ログイン時のレートリミット */
        'AUTH_USER_RATE_LIMIT' => 'auth_user_rate_limit_'
    ],

    'REDIS_TTL' => [
        'LOGIN_RATE_LIMIT' => 60 * 10, //10min
        "AUTH_TOKEN" => (60 * 60) * (24 * 7),//1week
        'AUTH_CODE' => 60 * 120,//2hour
        'PASSWORD_RESET' => 60 * 20, //20min
    ],

    'AUTH_TYPE' => [
        'EMAIL' => 'email',
        'TWITTER' => 'twitter'
    ],

    /** 管理画面の権限 */
    'ADMIN_AUTHORITY' => [
        'MASTER' => '1',
        'GENERAL' => '2'
    ]
];
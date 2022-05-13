<?php

namespace App\Services;


class BaseService
{

    public function trans_message(string $message_key)
    {
        return __($message_key);
    }
}
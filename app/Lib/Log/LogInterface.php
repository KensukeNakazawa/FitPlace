<?php

namespace App\Lib\Log;

interface LogInterface
{
    public function write(string $log_content, string $log_level);

    public function slackNotice(string $log_content, string $log_level);
}
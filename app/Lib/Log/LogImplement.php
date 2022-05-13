<?php

namespace App\Lib\Log;

use Illuminate\Support\Facades\Log;

class LogImplement implements LogInterface
{
    public function write(string $log_content, string $log_level)
    {
        switch ($log_level){
            case 'info':
                Log::channel('single')->info($log_content);
                break;
            case 'notice':
                Log::channel('single')->notice($log_content);
                break;
            case 'error':
                Log::channel('single')->error($log_content);
                break;
            case 'alert':
                Log::channel('single')->alert($log_content);
                break;
            case 'emergency':
                Log::channel('single')->emergency($log_content);
                break;
        }

    }

    public function slackNotice(string $log_content, string $log_level)
    {
        // TODO: Implement slackNotice() method.
    }

}
<?php

namespace App\Lib;

use Carbon\Carbon;

class DateImplement implements DateInterface
{
    /**
     * 今日が日曜日であれば今日だし，それ以外ではその週のスタート(月)-1
     * これはCarbonのが週初めを月曜日と設定しているため
     * @return Carbon
     */
    public function getStartOfWeek()
    {
        $date = Carbon::today();

        if ($date->dayOfWeek !== 0) {
            $date = $date->startOfWeek()->subDay(1);
        }
        /** デフォルトが週初め月曜日なため */
        return $date->startOfDay();
    }

    public function getEndOfWeek()
    {
        $date = Carbon::today();

        if ($date->dayOfWeek === 0) {
            $date = $date->addDay(6);
        } else {
            $date = $date->startOfWeek()->addDay(5);
        }

        return $date->endOfDay();
    }

    public function getTodayDayOfWeek(): int
    {
        $date = Carbon::today();
        return $date->dayOfWeek + 1;
    }


}
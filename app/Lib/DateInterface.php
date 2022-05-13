<?php

namespace App\Lib;

interface DateInterface
{
    public function getStartOfWeek();

    public function getEndOfWeek();

    /**
     * 今日の曜日のIDを取得する
     * 1: 日曜日 〜 7:土曜日
     * @return int
     */
    public function getTodayDayOfWeek() : int;
}
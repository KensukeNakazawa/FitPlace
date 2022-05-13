<?php

namespace Domain\Repositories\Notifies;

interface NotifyTimeRepositoryInterface
{

    /**
     * 全てを取得する
     * @return mixed
     */
    public function getAll();

    /**
     * notify_timeに該当するものを取得
     * @param int $notify_time_id
     * @return mixed
     */
    public function findByTime(int $notify_time_id);
}
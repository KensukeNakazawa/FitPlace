<?php

namespace Domain\Repositories\Notifies;

interface NotifySettingRepositoryInterface
{
    /**
     * 該当のユーザーIDのnotify_settingを全て取得する
     * @param int $user_id
     * @return mixed
     */
    public function getByUserId(int $user_id);

    /**
     * 時間によるNotifySettingを設定しているユーザーidを取得する
     * @param int $notify_time_id
     * @return mixed
     */
    public function getUserIdsByNotifyTime(int $notify_time_id);

    /**
     * 該当のnotify_settingを追加する
     * @param int $user_id
     * @param int $notify_time_id
     * @return mixed
     */
    public function store(int $user_id, int $notify_time_id);

    /**
     * 該当のnotify_settingを削除する
     * @param int $user_id
     * @param int $notify_time_id
     * @return mixed
     */
    public function delete(int $user_id, int $notify_time_id);
}
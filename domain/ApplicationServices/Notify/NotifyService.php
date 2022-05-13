<?php

namespace Domain\ApplicationServices\Notify;

use App\Models\User;

use Domain\Repositories\Notifies\NotifySettingRepositoryInterface;
use Domain\Repositories\Notifies\NotifyTimeRepositoryInterface;


class NotifyService
{

    private NotifySettingRepositoryInterface $notifySettingRepository;
    private NotifyTimeRepositoryInterface $notifyTimeRepository;

    /**
     * NotifyService constructor.
     * @param NotifySettingRepositoryInterface $notifySettingRepository
     * @param NotifyTimeRepositoryInterface $notifyTimeRepository
     */
    public function __construct(
        NotifySettingRepositoryInterface $notifySettingRepository,
        NotifyTimeRepositoryInterface $notifyTimeRepository
    ) {
        $this->notifySettingRepository = $notifySettingRepository;
        $this->notifyTimeRepository = $notifyTimeRepository;
    }


    /**
     * 対象のユーザーの通知希望時間と、全てのNotifyTimeを取得する
     * @param User $user
     * @return array
     */
    public function getMeNotifySetting(User $user)
    {
        $notify_settings = $this->notifySettingRepository->getByUserId($user->id);
        $notify_times = $this->notifyTimeRepository->getAll();

        return [
            'notify_settings' => $notify_settings,
            'notify_times' => $notify_times
        ];
    }

    /**
     * 通知希望の時間を更新する
     * @param User $user
     * @param array $notify_setting_ids
     */
    public function updateOrCreateNotifySetting(User $user, array $notify_setting_ids)
    {
        $notify_settings = $this->notifySettingRepository->getByUserId($user->id);
        $notify_times = $this->notifyTimeRepository->getAll();

        /**
         * 全てのNotifyTimeをチェックして、以下のパターンで更新を行う
         * 1. 入力されたnotify_setting_idsが現在の設定にない時 -> 新しく追加
         * 2. 入力されたnotify_setting_idsが現在の設定にある時 -> なにもしない
         * 3. 入力されていないnotify_setting_idsが現在の設定にある時 -> 該当の設定を削除
         */
        foreach ($notify_times as $notify_time)
        {
            if (in_array($notify_time->id, $notify_setting_ids)) {
                if ($notify_settings->where('notify_time_id', $notify_time->id)->isEmpty()) {
                    $this->notifySettingRepository->store($user->id, $notify_time->id);
                }
            } else {
                if ($notify_settings->where('notify_time_id', $notify_time->id)->isNotEmpty()) {
                    $this->notifySettingRepository->delete($user->id, $notify_time->id);
                }
            }
        }
    }

    public function checkLineNotify(User $user)
    {

    }
}
<?php

namespace App\Repositories\Notifies;

use App\Models\NotifySetting;

use Domain\Repositories\Notifies\NotifySettingRepositoryInterface;


class NotifySettingRepository implements NotifySettingRepositoryInterface
{

    private NotifySetting $model;

    /**
     * NotifySettingRepository constructor.
     * @param NotifySetting $model
     */
    public function __construct(NotifySetting $model)
    {
        $this->model = $model;
    }

    public function getByUserId(int $user_id)
    {
        return $this->model->newQuery()
            ->where('user_id', $user_id)
            ->orderBy('notify_time_id')
            ->get();
    }


    public function getUserIdsByNotifyTime(int $notify_time_id)
    {
        return $this->model->newQuery()
            ->where('notify_time_id', $notify_time_id)
            ->select(['user_id'])
            ->get();
    }

    public function store(int $user_id, int $notify_time_id)
    {
        return $this->model->newQuery()->create(
            [
                'user_id' => $user_id,
                'notify_time_id' => $notify_time_id
            ]
        );
    }

    public function delete(int $user_id, int $notify_time_id)
    {
        return $this->model->newQuery()->where(
            [
                'user_id' => $user_id,
                'notify_time_id' => $notify_time_id
            ]
        )->delete();
    }


}
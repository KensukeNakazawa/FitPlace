<?php

namespace App\Repositories\Notifies;

use App\Models\NotifyTime;

use Domain\Repositories\Notifies\NotifyTimeRepositoryInterface;


class NotifyTimeRepository implements NotifyTimeRepositoryInterface
{
    private NotifyTime $model;

    /**
     * NotifyTimeRepository constructor.
     * @param NotifyTime $model
     */
    public function __construct(NotifyTime $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->newQuery()->get();
    }


    public function findByTime($time)
    {
        return $this->model->newQuery()
            ->where('time', $time)
            ->first();
    }
}
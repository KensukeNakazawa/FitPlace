<?php

namespace App\Repositories\Notifies;

use App\Models\LineNotify;

use Domain\Repositories\Notifies\LineNotifyRepositoryInterface;

class LineNotifyRepository implements LineNotifyRepositoryInterface
{
    private LineNotify $model;

    /**
     * LineNotifyRepository constructor.
     * @param LineNotify $model
     */
    public function __construct(LineNotify $model)
    {
        $this->model = $model;
    }

    public function getByUserId(int $user_id)
    {
        return $this->model->newQuery()
            ->where('user_id', $user_id)
            ->get()->first();
    }


    public function checkNoteLineNotify(int $user_id)
    {
        $now = date('Y-m-d H:i:s', time());
        $this->model->newQuery()->updateOrInsert(
            ['user_id' => $user_id],
            ['check_at' => $now]
        );
    }

}
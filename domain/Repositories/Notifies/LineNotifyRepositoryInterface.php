<?php

namespace Domain\Repositories\Notifies;

interface LineNotifyRepositoryInterface
{
    public function getByUserId(int $user_id);

    public function checkNoteLineNotify(int $user_id);
}
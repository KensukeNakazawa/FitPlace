<?php

namespace Domain\ApplicationServices\Batch;

use App\Lib\DateInterface;
use Domain\Lib\ArrayInterface;

use Domain\ApplicationServices\Exercise\PlanExerciseService;
use Domain\ApplicationServices\Notify\LineNotifyService;

use Domain\Repositories\Notifies\NotifySettingRepositoryInterface;
use Domain\Repositories\Notifies\NotifyTimeRepositoryInterface;
use Domain\Repositories\Users\UserRepositoryInterface;


class SendPlanNotifyService
{
    private ArrayInterface $arrayInterface;
    private DateInterface $dateInterface;

    private PlanExerciseService $planExerciseService;
    private LineNotifyService $lineNotifyService;

    private NotifySettingRepositoryInterface $notifySettingRepository;
    private NotifyTimeRepositoryInterface $notifyTimeRepository;
    private UserRepositoryInterface $userRepository;

    /**
     * SendPlanNotifyService constructor.
     * @param ArrayInterface $arrayInterface
     * @param DateInterface $dateInterface
     * @param PlanExerciseService $planExerciseService
     * @param LineNotifyService $lineNotifyService
     * @param NotifySettingRepositoryInterface $notifySettingRepository
     * @param NotifyTimeRepositoryInterface $notifyTimeRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        ArrayInterface $arrayInterface,
        DateInterface $dateInterface,
        PlanExerciseService $planExerciseService,
        LineNotifyService $lineNotifyService,
        NotifySettingRepositoryInterface $notifySettingRepository,
        NotifyTimeRepositoryInterface $notifyTimeRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->arrayInterface = $arrayInterface;
        $this->dateInterface = $dateInterface;
        $this->planExerciseService = $planExerciseService;
        $this->lineNotifyService = $lineNotifyService;
        $this->notifySettingRepository = $notifySettingRepository;
        $this->notifyTimeRepository = $notifyTimeRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * 対象の時間に通知を希望しているユーザーにトレーニング予定の通知を行う
     * @param $time
     */
    final public function SendNotifyAtHour($time)
    {
        $users = $this->getTargetUser($time);
        $day_of_week = $this->dateInterface->getTodayDayOfWeek();
        foreach ($users as $user) {
            $result = $this->planExerciseService->getPlanExercise($user, $day_of_week);

            if (empty($result)) {
                continue;
            }

            $send_content = "\n今日は予定されているトレーニングがあります!\n\n";

            foreach ($result as $data) {
                $send_content .= '・' . $data['exercise_name'] . $data['set'] . "セット\n";
            }

            $send_content .= "が予定されています！\n";
            $send_content .= "予定のトレーニングに取り組みましょう。\n";

            $send_content .= config('app.url') . 'home';
            $send_content .= '?openExternalBrowser=1';

            $this->lineNotifyService->sendLineNotify($user->getAccessToken(), $send_content);
        }

    }

    /**
     * @param $time
     * @return mixed
     */
    private function getTargetUser($time)
    {
        $notify_time = $this->notifyTimeRepository->findByTime($time);
        $notify_setting_user_ids = $this->notifySettingRepository->getUserIdsByNotifyTime($notify_time->id);

        $notify_setting_user_ids = $this->arrayInterface->flatten($notify_setting_user_ids->toArray());
        $users = $this->userRepository->getUsersById($notify_setting_user_ids);

        return $users;
    }

}
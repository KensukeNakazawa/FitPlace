<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\WeekDay;


class WeekDayController extends Controller
{
    private WeekDay $weekDayModel;

    /**
     * WeekDayController constructor.
     * @param WeekDay $weekDayModel
     */
    public function __construct(WeekDay $weekDayModel)
    {
        $this->weekDayModel = $weekDayModel;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $week_days = $this->weekDayModel->newQuery()->select(['id', 'name'])->get();

        return response()->json($week_days);
    }

}

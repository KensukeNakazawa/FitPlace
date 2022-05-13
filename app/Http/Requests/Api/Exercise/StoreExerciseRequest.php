<?php

namespace App\Http\Requests\Api\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'exercise_type_id' => 'required|integer',
            'exercise_details' => 'required|array|min:1',
            'date' => 'required|date'
        ];
    }
}

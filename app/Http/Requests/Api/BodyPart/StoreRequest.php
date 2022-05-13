<?php

namespace App\Http\Requests\Api\BodyPart;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'body_part_id' => 'required',
            'exercise_type_name' => 'required|string|max:20'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
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
            'clock_in_time'  => ['required', 'date_format:H:i'],
            'clock_out_time' => ['required', 'date_format:H:i'],

            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i'],

            'reason' => ['required', 'string', 'max:255'],
        ];
    }
    public function messages()
    {
        return [
            'clock_in_time.required'  => '出勤時間は必須です',
            'clock_out_time.required' => '退勤時間は必須です',

            'breaks.*.start.required_with' => '休憩開始と終了はセットで入力してください',
            'breaks.*.end.required_with'   => '休憩開始と終了はセットで入力してください',

            'reason.required' => '備考（修正理由）は必須です',
        ];
    }
}

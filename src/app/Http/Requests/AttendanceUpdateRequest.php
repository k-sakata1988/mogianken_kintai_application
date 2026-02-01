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

            'reason.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $in  = $this->input('clock_in_time');
            $out = $this->input('clock_out_time');

            if ($in && $out && $in >= $out) {
                $validator->errors()->add(
                    'clock_in_time',
                    '出勤時間、もしくは退勤時間が不適切な値です'
                );
            }

            foreach ($this->input('breaks', []) as $break) {
                if (
                    !empty($break['start']) &&
                    !empty($out) &&
                    $break['start'] >= $out
                ) {
                    $validator->errors()->add(
                        'breaks',
                        '休憩時間、もしくは退勤時間が不適切な値です'
                    );
                }

                if (
                    !empty($break['end']) &&
                    !empty($out) &&
                    $break['end'] >= $out
                ) {
                    $validator->errors()->add(
                        'breaks',
                        '休憩時間、もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}

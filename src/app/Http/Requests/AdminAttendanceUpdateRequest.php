<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
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
            'clock_in_time'  => ['required'],
            'clock_out_time' => ['required'],
            'remark'         => ['required'],
            'break_start'    => ['nullable'],
            'break_end'      => ['nullable'],
        ];
    }
    public function messages(): array
    {
        return [
            'clock_in_time.required'  => '出勤時間は必須です。',
            'clock_out_time.required' => '退勤時間は必須です。',
            'remark.required'         => '備考欄は必須です。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->clock_in_time || !$this->clock_out_time) {
                return;
            }

            $date = $this->route('attendance')->date->format('Y-m-d');

            $clockIn  = Carbon::parse($date.' '.$this->clock_in_time);
            $clockOut = Carbon::parse($date.' '.$this->clock_out_time);

            if ($clockIn->gt($clockOut)) {
                $validator->errors()->add(
                    'clock_in_time',
                    '出勤時間、もしくは退勤時間が不適切な値です。'
                );
            }

            if ($this->filled('break_start')) {
                $breakStart = Carbon::parse($date.' '.$this->break_start);

                if ($breakStart->lt($clockIn) || $breakStart->gt($clockOut)) {
                    $validator->errors()->add(
                        'break_start',
                        '休憩時間が不適切な値です。'
                    );
                }
            }

            if ($this->filled('break_end')) {
                $breakEnd = Carbon::parse($date.' '.$this->break_end);

                if ($breakEnd->gt($clockOut)) {
                    $validator->errors()->add(
                        'break_end',
                        '休憩時間、もしくは退勤時間が不適切な値です。'
                    );
                }
            }
        });
    }
}

<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

// 追加
use Illuminate\Contracts\Validation\Validator;

class UpdateAttendanceRequest extends FormRequest
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
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i|after:clock_in',
            'note' => 'required|string',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $in = $this->input('clock_in');
            $out = $this->input('clock_out');
            $breaks = $this->input('breaks', []);

            foreach ($breaks as $index => $break) {
                $start = $break['start'] ?? null;
                $end = $break['end'] ?? null;

                if ($start && ($start < $in || $start > $out)) {
                    $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                }

                if ($end && ($end < $in || $end > $out)) {
                    $validator->errors()->add("breaks.$index.end", '休憩時間が不適切な値です');
                }
            }
        });
    }
}

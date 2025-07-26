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
            'clock_out' => 'required|date_format:H:i',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i',
            'note' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '出勤時間の形式が正しくありません(「HH:MM」形式で入力してください)',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '退勤時間の形式が正しくありません(「HH:MM」形式で入力してください)',
            'breaks.*.start.date_format' => '休憩開始時間の形式が正しくありません(「HH:MM」形式で入力してください)',
            'breaks.*.end.date_format' => '休憩終了時間の形式が正しくありません(「HH:MM」形式で入力してください)',
            'note.required' => '備考を記入してください',
            'note.string' => '備考は文字列で入力してください',
            'note.max' => '備考は255文字以内で入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            // 出勤時間と退勤時間の整合性
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_out', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩時間の整合性
            $breaks = $this->input('breaks', []);
            foreach ($breaks as $index => $break) {
                $start = $break['start'] ?? null;
                $end = $break['end'] ?? null;

                // 両方空なら無視
                if (empty($start) && empty($end)) {
                    continue;
                }

                // 休憩開始時間が退勤後
                if ($start && $clockOut && $start > $clockOut) {
                    $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                }

                // 休憩終了時間が退勤後
                if ($end && $clockOut && $end > $clockOut) {
                    $validator->errors()->add('clock_out', '出勤時間もしくは退勤時間が不適切な値です');
                }

                // 休憩開始時間が勤務時間外
                if ($start && $clockIn && $start < $clockIn) {
                    $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                }

                // 休憩終了時間が勤務時間外
                if ($end && $clockIn && $end < $clockIn) {
                    $validator->errors()->add("breaks.$index.end", '出勤時間もしくは退勤時間が不適切な値です');
                }

                // 休憩開始 ≥ 終了
                if ($start && $end && $start >= $end) {
                    $validator->errors()->add("breaks.$index.end", '休憩終了時間は開始時間より後にしてください。');
                }
            }
        });
    }
}

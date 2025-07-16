<?php

namespace App\Http\Requests\Admin\Attendance;

use Illuminate\Foundation\Http\FormRequest;

// 追加
use Carbon\Carbon;

class AdminUpdateAttendanceRequest extends FormRequest
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
            'note' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '出勤時間の形式が正しくありません',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '退勤時間の形式が正しくありません',
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        // 出勤・退勤時間
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            try {
                $clockInTime = \Carbon\Carbon::createFromFormat('H:i', $clockIn);
                $clockOutTime = \Carbon\Carbon::createFromFormat('H:i', $clockOut);
            } catch (\Exception $e) {
                // フォーマット不正ならスキップ
                return;
            }

            if ($clockInTime->gte($clockOutTime)) {
                $validator->errors()->add('clock_out', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩時間
            $breaks = $this->input('breaks', []);
            foreach ($breaks as $index => $break) {
                $start = $break['start'] ?? null;
                $end = $break['end'] ?? null;

                // 空行はスキップ（''もnullも除外）
                if (empty($start) && empty($end)) {
                    continue;
                }

                try {
                    if ($start) {
                        $startTime = Carbon::createFromFormat('H:i', $start);
                        if ($startTime->lt($clockInTime) && $startTime->gt($clockOutTime)) {
                            $validator->errors()->add("breaks.$index.start", '出勤時間もしくは退勤時間が不適切な値です');
                        }
                    }

                    if ($end) {
                        $endTime = Carbon::createFromFormat('H:i', $end);
                        if ($endTime->lt($clockInTime) || $endTime->gt($clockOutTime)) {
                            $validator->errors()->add("breaks.$index.end", '出勤時間もしくは退勤時間が不適切な値です');
                        }
                    }
                } catch (\Exception $e) {
                    // 時刻形式が不正な場合は無視
                    continue;
                }
            }
        });
    }
}

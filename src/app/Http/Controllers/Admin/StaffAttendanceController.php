<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 追加
use Carbon\Carbon;

// モデル追加
use App\Models\User;
use App\Models\Attendance;

// CSV出力機能付与のために追加
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffAttendanceController extends Controller
{
    // スタッフ別勤怠一覧画面（管理者）
    public function index(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $date = $request->input('month')
            ? Carbon::parse($request->input('month') . '-01')
            : Carbon::now()->startOfMonth();

        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $attendanceRecords = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('date');

        $attendances = [];
        $daysInMonth = $date->daysInMonth;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = $date->copy()->day($i)->toDateString();
            $carbonDate = $date->copy()->day($i);

            if ($attendanceRecords->has($currentDate)) {
                $record = $attendanceRecords[$currentDate];

                // 休憩合計（分）
                $breakMinutes = $record->breaks->reduce(function ($carry, $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end = Carbon::parse($break->break_end);
                        return $carry + $end->diffInMinutes($start);
                    }
                    return $carry;
                }, 0);

                $attendances[] = (object)[
                    'id' => $record->id,
                    'date' => $carbonDate,
                    'clock_in' => $record->clock_in,
                    'clock_out' => $record->clock_out,
                    'break_time' => $this->formatMinutes($breakMinutes),
                    'total_time' => $this->formatMinutes(
                        ($record->clock_in && $record->clock_out)
                            ? Carbon::parse($record->clock_in)->diffInMinutes($record->clock_out) - $breakMinutes
                            : null
                    ),
                ];
            } else {
                // 該当日が記録されていない
                $attendances[] = (object)[
                    'id' => null,
                    'date' => $carbonDate,
                    'clock_in' => '',
                    'clock_out' => '',
                    'break_time' => '',
                    'total_time' => '',
                ];
            }
        }

        return view('admin.staff_attendance.index', compact('attendances', 'user', 'date'));
    }

    // CSV出力処理
    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $month = $request->input('month');
        $date = $month ? Carbon::parse($month . '-01') : Carbon::now()->startOfMonth();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $attendanceRecords = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('date');

        $daysInMonth = $date->daysInMonth;
        $csvData = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = $date->copy()->day($i)->toDateString();
            $carbonDate = $date->copy()->day($i);

            if ($attendanceRecords->has($currentDate)) {
                $record = $attendanceRecords[$currentDate];

                $breakMinutes = $record->breaks->reduce(function ($carry, $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end = Carbon::parse($break->break_end);
                        return $carry + $end->diffInMinutes($start);
                    }
                    return $carry;
                }, 0);

                $csvData[] = [
                    '日付' => $carbonDate->format('Y-m-d'),
                    '出勤' => optional($record->clock_in)->format('H:i') ?? '',
                    '退勤' => optional($record->clock_out)->format('H:i') ?? '',
                    '休憩' => $this->formatMinutes($breakMinutes),
                    '合計' => $this->formatMinutes($record->worked_minutes),
                ];
            } else {
                $csvData[] = [
                    '日付' => $carbonDate->format('Y-m-d'),
                    '出勤' => '',
                    '退勤' => '',
                    '休憩' => '',
                    '合計' => '',
                ];
            }
        }

        $fileName = "attendance_{$user->id}_{$date->format('Y_m')}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_keys($csvData[0]));
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function formatMinutes($minutes)
    {
        if (is_null($minutes)) return '';
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%d:%02d', $hours, $mins);
    }
}

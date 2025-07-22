<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 追加
use Carbon\Carbon;

// CSV出力機能付与のために追加
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffAttendanceController extends Controller
{
    public function index(Request $request, $id)
    {
        // 対象月（指定がなければ今月）
        $date = $request->input('month')
            ? Carbon::parse($request->input('month') . '-01')
            : Carbon::now()->startOfMonth();

        // 月の日数分のダミーデータを作成
        $daysInMonth = $date->daysInMonth;
        $attendances = [];

        // ✅ ここで休日リストを定義する（3, 4, 11, 18, 25）
        $holidays = [3, 4, 11, 18, 25];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = $date->copy()->day($i);

            // 休みなら空白
            if (in_array($i, $holidays)) {
                $attendances[] = (object)[
                    'id' => $i,
                    'date' => $day,
                    'clock_in' => '',
                    'clock_out' => '',
                    'break_time' => '',
                    'total_time' => '',
                ];
            } else {
                $attendances[] = (object)[
                    'id' => $i,
                    'date' => $day,
                    'clock_in' => '09:00',
                    'clock_out' => '18:00',
                    'break_time' => '1:00',
                    'total_time' => '8:00',
                ];
            }
        }

        $user = (object)[
            'id' => $id,
            'name' => '西伶奈'
        ];

        return view('admin.staff_attendance.index', compact('attendances', 'user', 'date'));
    }

    public function exportCsv(Request $request, $id)
    {
        $month = $request->input('month');
        $date = $month ? Carbon::parse($month . '-01') : Carbon::now()->startOfMonth();
        $daysInMonth = $date->daysInMonth;

         // ✅ 休日リスト（この日にちは出勤データを空白にする）
        $holidays = [3, 4, 11, 18, 25];

        // ダミーデータ生成（実際はDBから取得）
        $attendances = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = $date->copy()->day($i);

            if (in_array($i, $holidays)) {
                // 休日は空白
                $attendances[] = [
                    '日付' => $day->format('Y-m-d'),
                    '出勤' => '',
                    '退勤' => '',
                    '休憩' => '',
                    '合計' => '',
                ];
            } else {
                $attendances[] = [
                    '日付' => $day->format('Y-m-d'),
                    '出勤' => '09:00',
                    '退勤' => '18:00',
                    '休憩' => '1:00',
                    '合計' => '8:00',
                ];
            }
        }

        $fileName = "attendance_{$id}_{$date->format('Y_m')}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_keys($attendances[0])); // ヘッダー行
            foreach ($attendances as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

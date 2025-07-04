<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 勤怠情報取得機能・勤怠詳細表示機能
use App\Models\Attendance;

// 日時変更機能
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // // 勤怠一覧画面(管理者)
    public function index(Request $request)
    {
    //     // 指定された日付、もしくは今日
    //     $date = $request->input('date')
    //         ? Carbon::parse($request->input('date'))
    //         : Carbon::today();

    //     // 勤怠情報を日付単位で取得（リレーションでユーザー情報も取得）
    //     $attendances = Attendance::with(['user', 'breaks'])
    //         ->whereDate('date', $date->toDateString())
    //         ->get();

    //     // 各勤怠に対して動的な項目を追加
    //     foreach ($attendances as $attendance) {
    //         // 出退勤がある場合のみ処理
    //         if ($attendance->clock_in && $attendance->clock_out) {
    //             $clockIn = Carbon::parse($attendance->clock_in);
    //             $clockOut = Carbon::parse($attendance->clock_out);

    //             // 総勤務時間（秒）
    //             $totalSeconds = $clockOut->diffInSeconds($clockIn);

    //             // 休憩時間（秒）を breaks から計算
    //             $breakSeconds = $attendance->breaks->reduce(function ($carry, $break) {
    //                 if ($break->break_start && $break->break_end) {
    //                     return $carry + Carbon::parse($break->break_end)->diffInSeconds(Carbon::parse($break->break_start));
    //                 }
    //                 return $carry;
    //             }, 0);

    //             // 実働時間（総勤務 - 休憩）
    //             $workedSeconds = max(0, $totalSeconds - $breakSeconds);

    //             // 動的プロパティにフォーマット付きで代入
    //             $attendance->break_duration = gmdate('H:i', $breakSeconds);
    //             $attendance->worked_duration = gmdate('H:i', $workedSeconds);
    //         } else {
    //             $attendance->break_duration = '0:00';
    //             $attendance->worked_duration = '0:00';
    //         }
    //     }

        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        // ダミー出勤・退勤時刻
        $clockIn = $date->copy()->setTime(9, 0);
        $clockOut = $date->copy()->setTime(18, 0);

        // ダミー休憩時間
        $breakDuration = 3600; // 秒（1時間）

        // 実働時間
        $workedDuration = $clockOut->diffInSeconds($clockIn) - $breakDuration;

        // ダミーAttendanceコレクション作成
        $attendances = collect([
            (object)[
                'id' => 1,
                'user' => (object)['name' => 'テストユーザー'],
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'break_duration' => gmdate('H:i', $breakDuration),
                'worked_duration' => gmdate('H:i', $workedDuration),
            ],
            // 2人目など追加可能
        ]);

        return view('admin.attendance.index', compact('attendances', 'date'));
    }

    // 勤怠詳細画面(管理者)
    public function show($id)
    {
        // ダミーの日付と時刻
        $date = \Carbon\Carbon::create(2023, 6, 1);
        $clockIn = $date->copy()->setTime(9, 0);
        $clockOut = $date->copy()->setTime(20, 0);

        // ダミー休憩
        $breaks = collect([
            (object)[
                'break_start' => $date->copy()->setTime(12, 0),
                'break_end' => $date->copy()->setTime(13, 0),
            ],
            // 休憩2を追加したければ以下も追加可能
            // (object)[
            //     'break_start' => $date->copy()->setTime(15, 30),
            //     'break_end' => $date->copy()->setTime(15, 45),
            // ],
        ]);

        // ダミー勤怠データ（Attendance風オブジェクト）
        $attendance = (object)[
            'id' => $id,
            'user' => (object)[ 'name' => '西 伶奈' ],
            'date' => $date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'note' => '',
            'breaks' => $breaks,
        ];

        return view('admin.attendance.show', compact('attendance'));
    }

    // 勤怠詳細画面(管理者)での編集メソッド
    public function update(Request $request, $id)
    {
        // 仮のリダイレクト処理
        return redirect()->route('admin.attendance.show', $id)->with('success', '更新処理は未実装です');
    }
}

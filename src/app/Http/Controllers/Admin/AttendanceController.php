<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// フォームリクエスト実装(AdminUpdateAttendanceRequest.php)
use App\Http\Requests\Admin\Attendance\AdminUpdateAttendanceRequest;

// 勤怠情報取得機能・勤怠詳細表示機能
use App\Models\Attendance;

// 日時変更機能
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // // 勤怠一覧画面(管理者)
    public function index(Request $request)
    {
        // 本番実装で解除
        // $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        // $attendances = Attendance::with(['user', 'breaks'])
        //     ->whereDate('date', $date->toDateString())
        //     ->get();

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
        // 本番実装で解除
        // $attendance = Attendance::with(['user', 'breaks'])->findOrFail($id);

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
    public function update(AdminUpdateAttendanceRequest $request, $id)
    {
        // $attendance = Attendance::with('breaks')->findOrFail($id);

        // $attendance->clock_in = $request->input('clock_in');
        // $attendance->clock_out = $request->input('clock_out');
        // $attendance->note = $request->input('note');
        // $attendance->save();

        // // 既存の休憩時間を削除・再登録でも可
        // $attendance->breaks()->delete();
        // foreach ($request->input('breaks', []) as $break) {
        //     if (!empty($break['start']) || !empty($break['end'])) {
        //         $attendance->breaks()->create([
        //             'break_start' => $break['start'],
        //             'break_end' => $break['end'],
        //         ]);
        //     }
        // }

        // 入力内容をセッションに保存
        session()->put("attendance_update.{$id}", [
            'id' => $id,
            'name' => '西 伶奈', // 本来なら $attendance->user->name
            'date' => '2023年6月1日', // 本来なら $attendance->date->formatなど
            'start_time' => $request->input('clock_in'),
            'end_time' => $request->input('clock_out'),
            'break1_start' => $request->input('breaks')[0]['start'] ?? null,
            'break1_end' => $request->input('breaks')[0]['end'] ?? null,
            'break2_start' => $request->input('breaks')[1]['start'] ?? null,
            'break2_end' => $request->input('breaks')[1]['end'] ?? null,
            'note' => $request->input('note'),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.approval.show', $id)->with('success', '勤怠情報を更新しました');
    }
}

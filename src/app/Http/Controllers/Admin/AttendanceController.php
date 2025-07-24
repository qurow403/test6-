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
        // 任意の日付
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $attendances = Attendance::with(['user', 'breaks'])
            ->whereDate('date', $date->toDateString())
            ->get();

        return view('admin.attendance.index', compact('attendances', 'date'));
    }

    // 勤怠詳細画面(管理者)
    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breaks'])->findOrFail($id);

        return view('admin.attendance.show', compact('attendance'));
    }

    // 勤怠詳細画面(管理者)での編集メソッド
    public function update(AdminUpdateAttendanceRequest $request, $id)
    {
        $attendance = Attendance::with('breaks')->findOrFail($id);

        $attendance->clock_in = $request->input('clock_in');
        $attendance->clock_out = $request->input('clock_out');
        $attendance->note = $request->input('note');
        $attendance->save();

        // 既存の休憩削除 → 入力に基づき再登録
        $attendance->breaks()->delete();

        foreach ($request->input('breaks', []) as $break) {
            if (!empty($break['start']) || !empty($break['end'])) {
                $attendance->breaks()->create([
                    'break_start' => $break['start'],
                    'break_end' => $break['end'],
                ]);
            }
        }

        return redirect()->route('admin.approval.show', $id)->with('success', '勤怠情報を更新しました');
    }
}

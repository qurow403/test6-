<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// 勤怠詳細画面(一般ユーザー)でのバリデーション実装のために追加
use App\Http\Requests\Attendance\UpdateAttendanceRequest;


// Attendance・Break(Time)モデル・時間追加
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 出勤登録画面(一般ユーザー)
    public function create()
    {
        // ダミーの現在時刻
        $now = Carbon::create(2023, 6, 1, 8, 0, 0); // 表示用

        // ダミー状態（本来はDBから今日の勤怠情報取得して判定）
        // 状態: before, working, on_break, finished
        $status = 'before'; // 初期状態

        // 状態を切り替えるサンプル（本番ではユーザーや今日の打刻情報から判定）
        // ここでランダムに状態切り替え（テスト表示用）
        // $status = collect(['before', 'working', 'on_break', 'finished'])->random();

        return view('attendance.create', compact('now', 'status'));
    }

    // 勤怠登録処理(一般ユーザー)
    public function handleAction(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'clock_in':
                return $this->clockIn($request);
            case 'clock_out':
                return $this->clockOut($request);
            case 'break_in':
                return $this->breakStart($request);
            case 'break_out':
                return $this->breakEnd($request);
            default:
                return back()->with('error', '無効な操作です');
        }
    }

    // 出勤 clockIn
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $today = Carbon::today();

        // 同じ日付の出勤が既にあるかチェック
        $already = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($already && $already->clock_in) {
            return back()->with('error', '本日はすでに出勤済みです');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'clock_in' => now(),
            'status' => 'working',
        ]);

        return redirect()->route('attendance.create')->with('success', '出勤しました');
    }

    // 休憩開始 breakStart
    public function breakStart(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'まず出勤をしてください');
        }

        // 休憩中でないことを確認
        if ($attendance->status === 'on_break') {
            return back()->with('error', 'すでに休憩中です');
        }

        // 新しい休憩レコード作成（end未入力）
        $attendance->breaks()->create([
            'break_start' => now(),
        ]);

        // ステータス更新
        $attendance->update(['status' => 'on_break']);

        return redirect()->route('attendance.create')->with('success', '休憩開始しました');
    }

    // 休憩終了 breakEnd
    public function breakEnd(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with('breaks')
            ->first();

        if (!$attendance || $attendance->status !== 'on_break') {
            return back()->with('error', '現在は休憩中ではありません');
        }

        // 最新の break レコード取得（break_end が null のもの）
        $latestBreak = $attendance->breaks()
            ->whereNull('break_end')
            ->latest()
            ->first();

        if (!$latestBreak) {
            return back()->with('error', '休憩開始記録が見つかりません');
        }

        $latestBreak->update(['break_end' => now()]);

        // ステータス更新
        $attendance->update(['status' => 'working']);

        return redirect()->route('attendance.create')->with('success', '休憩終了しました');
    }

    // 退勤 clockOut
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'まず出勤してください');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'すでに退勤済みです');
        }

        if ($attendance->status === 'on_break') {
            return back()->with('error', '休憩終了後に退勤してください');
        }

        $breakMinutes = $attendance->breaks->sum(function ($break) {
            return $break->break_start && $break->break_end
                ? Carbon::parse($break->break_end)->diffInMinutes($break->break_start)
                : 0;
        });

        $workedMinutes = Carbon::parse($attendance->clock_in)->diffInMinutes(now()) - $breakMinutes;

        $attendance->update([
            'clock_out' => now(),
            'status' => 'finished',
            'worked_minutes' => max($workedMinutes, 0),
        ]);

        return redirect()->route('attendance.create')->with('success', '退勤しました。お疲れ様でした。');
    }

    // 勤怠一覧画面(一般ユーザー)
    public function index(Request $request)
    {
        // ルートパラメータ ?month=2023-07 などを取得（なければ現在の月）
        $currentMonth = $request->input('month', Carbon::now()->format('Y-m'));

        // 現在の月をCarbonインスタンスとして扱う
        $current = Carbon::parse($currentMonth);

        // 前月・翌月の文字列を生成
        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');

        // 表示対象の月の日付範囲を生成
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        // ダミー勤怠データ生成（本番はDBから取得）
        $attendances = collect();
        $id = 1;

        for ($date = $start; $date->lte($end); $date->addDay()) {
            // 土日も表示したいならこのままでOK（除外したいなら $date->isWeekday() チェック）
            $attendances->push((object)[
                'id' => $id++,
                'date' => $date->format('Y-m-d'),
                'clock_in' => $date->copy()->setTime(9, 0, 0)->toDateTimeString(),
                'clock_out' => $date->copy()->setTime(18, 0, 0)->toDateTimeString(),
                'break1' => $date->copy()->setTime(12, 0, 0)->toDateTimeString(),
                'break2' => $date->copy()->setTime(13, 0, 0)->toDateTimeString(),
                'status' => 'finished',
            ]);
        }

        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        return view('attendance.index', compact('attendances', 'currentMonth', 'prevMonth', 'nextMonth', 'weekdays'));
    }

    // 勤怠詳細画面(一般ユーザー)
    public function show($id)
    {
        $attendance = (object)[
            'id' => $id,
            'user_name' => '西 伶奈',
            'date' => '2023-06-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '電車遅延のため',
            'breaks' => [
                ['start' => '12:00', 'end' => '13:00'],
                ['start' => '', 'end' => ''],
            ]
        ];

        return view('attendance.show', compact('attendance'));
    }

    // 勤怠詳細画面(一般ユーザー)で修正申請するメソッド
    public function update(UpdateAttendanceRequest $request, $id)
    {
        // バリデーションルール通過済のデータ取得
        $validated = $request->validated();

        // ダミーデータとして user_name や date を追加
        $validated['user_name'] = '西 伶奈';
        $validated['date'] = '2023年6月1日';

        logger()->info("修正申請内容", $validated);

        return redirect()->route('attendance.pending', $id)
            ->with('success', '修正申請を送信しました（承認待ち）')
            ->with('submitted', $validated);
    }

    // 勤怠詳細画面＿承認待ち(一般ユーザー)
    public function pending($id)
    {
        $submitted = session('submitted'); // セッションから取得

        // if (!$submitted) {
        //     return redirect()->route('attendance.index')->with('error', '表示できるデータがありません');
        // }

        // clock_in / clock_out を H:i 形式に整形（nullチェック付き）
        $clockIn = isset($submitted['clock_in']) && $submitted['clock_in']
            ? date('H:i', strtotime($submitted['clock_in']))
            : '未入力';

        $clockOut = isset($submitted['clock_out']) && $submitted['clock_out']
            ? date('H:i', strtotime($submitted['clock_out']))
            : '未入力';

        // breaks を H:i 形式で整形（breaks が存在する場合のみ）
        $breaks = collect($submitted['breaks'] ?? [])->map(function ($break) {
            return [
                'start' => isset($break['start']) && $break['start']
                    ? date('H:i', strtotime($break['start']))
                    : '-',
                'end' => isset($break['end']) && $break['end']
                    ? date('H:i', strtotime($break['end']))
                    : '-',
            ];
        })->toArray();

        // ダミーデータ含めたattendanceオブジェクト生成
        $attendance = (object)[
            'id' => $id,
            'user_name' => $submitted['user_name'] ?? '未設定ユーザー',
            'date' => $submitted['date'] ?? '未設定日付',
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'breaks' => $breaks,
            'note' => $submitted['note'] ?? '（備考なし）',
            'message' => '✳︎承認待ちのため修正できません。'
        ];

        return view('attendance.pending', compact('attendance', 'id'));
    }
}

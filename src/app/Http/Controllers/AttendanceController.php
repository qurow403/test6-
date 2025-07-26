<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// 勤怠詳細画面(一般ユーザー)でのバリデーション実装のために追加
use App\Http\Requests\Attendance\UpdateAttendanceRequest;

// フォームリクエスト追加
use App\Http\Requests\Attendance\AttendanceActionRequest;

// Attendance・Break(Time)モデル・時間追加
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 出勤登録画面(一般ユーザー)
    public function create()
    {
        $now = Carbon::now(); // 現在日時を取得（本番用）

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $now->toDateString())
            ->first();

        $status = $attendance->status ?? Attendance::STATUS_BEFORE;

        return view('attendance.create', compact('now', 'status'));
    }

    // 勤怠登録処理(一般ユーザー)
    public function handleAction(AttendanceActionRequest $request)
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
            'status' => Attendance::STATUS_WORKING,
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
        if ($attendance->status === Attendance::STATUS_ON_BREAK) {
            return back()->with('error', 'すでに休憩中です');
        }

        // 新しい休憩レコード作成（end未入力）
        $attendance->breaks()->create([
            'break_start' => now(),
        ]);

        // ステータス更新
        $attendance->update(['status' => Attendance::STATUS_ON_BREAK]);

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

        if (!$attendance || $attendance->status !== Attendance::STATUS_ON_BREAK) {
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
        $attendance->update(['status' => Attendance::STATUS_WORKING]);

        return redirect()->route('attendance.create')->with('success', '休憩終了しました');
    }

    // 退勤 clockOut
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with('breaks')
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'まず出勤してください');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'すでに退勤済みです');
        }

        if ($attendance->status === Attendance::STATUS_ON_BREAK) {
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
            'status' => Attendance::STATUS_FINISHED,
            'worked_minutes' => max($workedMinutes, 0),
        ]);

        return redirect()->route('attendance.create')->with('success', '退勤しました。お疲れ様でした。');
    }

    // 勤怠一覧画面(一般ユーザー)
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $currentMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $current = Carbon::parse($currentMonth);
        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');

        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        // 勤怠情報をDBから取得（ログインユーザーの今月分）
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->with('breaks') // 休憩も取得
            ->orderBy('date')
            ->get();

        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        return view('attendance.index', compact('attendances', 'currentMonth', 'prevMonth', 'nextMonth', 'weekdays'));
    }

    // 勤怠詳細画面(一般ユーザー)
    public function show($id)
    {
        $attendance = Attendance::with(['breaks', 'user'])->findOrFail($id);

        return view('attendance.show', compact('attendance'));
    }

    // 勤怠詳細画面(一般ユーザー)で修正申請するメソッド
    public function update(UpdateAttendanceRequest $request, $id)
    {
        // バリデーションルール通過済のデータ取得、バリデーション済データ取得
        $validated = $request->validated();

        // 該当勤怠データを取得（userも取得して名前参照）
        $attendance = Attendance::with('user')->findOrFail($id);

        // 勤怠情報を更新して「承認待ち」にする
        $attendance->update([
            'clock_in' => $validated['clock_in'],
            'clock_out' => $validated['clock_out'],
            'note' => $validated['note'],
            'request_status' => Attendance::STATUS_PENDING,
        ]);

        // 既存のBreakTimeを削除（初期化）
        $attendance->breaks()->delete();

        // 新しく送られてきたbreaks配列から再登録
        if ($request->has('breaks')) {
            foreach ($request->input('breaks') as $breakData) {
                if (!empty($breakData['start']) && !empty($breakData['end'])) {
                    $attendance->breaks()->create([
                        'break_start' => $breakData['start'],
                        'break_end' => $breakData['end'],
                    ]);
                }
            }
        }

        return redirect()->route('attendance.pending', $id)
            ->with('success', '修正申請を送信しました（承認待ち）')
            ->with('submitted', $validated);
    }

    // 勤怠詳細画面＿承認待ち(一般ユーザー)
    public function pending($id)
    {
        // 自分の勤怠で、ステータスが承認待ちのものを取得
        $attendance = Attendance::with(['breaks', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('request_status', Attendance::STATUS_PENDING)
            ->firstOrFail();

        return view('attendance.pending', compact('attendance', 'id'));
    }
}

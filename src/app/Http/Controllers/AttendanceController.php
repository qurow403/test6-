<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// コントローラーを使えるようにするため追加
use App\Http\Controllers\Controller;

// 勤怠詳細画面(一般ユーザー)でのバリデーション実装のために追加
use App\Http\Requests\Attendance\UpdateAttendanceRequest;


// Attendanceモデル・時間追加
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 出勤登録画面(一般ユーザー)
    public function create()
    {
        return view('attendance.create'); // resources/views/attendance/create.blade.php を返す
    }

    // 勤怠一覧画面(一般ユーザー)
    public function index(Request $request)
    {
        // $user = auth()->user();
        // $month = $request->input('month') ?? now()->format('Y-m');
        // $start = Carbon::parse($month)->startOfMonth();
        // $end = Carbon::parse($month)->endOfMonth();

        // $attendances = Attendance::where('user_id', $user->id)
        // ->whereBetween('date', [$start, $end])
        // ->orderBy('date')
        // ->get();

        // return view('attendance.index', [
        //     'attendances' => $attendances,
        //     'currentMonth' => $month,
        //     'prevMonth' => Carbon::parse($month)->subMonth()->format('Y-m'),
        //     'nextMonth' => Carbon::parse($month)->addMonth()->format('Y-m'),
        //     'weekdays' => ['日', '月', '火', '水', '木', '金', '土']
        // ]);

        // ログインユーザー無しのため、ダミーデータを作る
        $attendances = collect([
            (object)[
                'id' => 1,
                'date' => '2025-06-01',
                'clock_in' => '2025-06-01 09:00:00',
                'clock_out' => '2025-06-01 18:00:00',
                'break1' => '2025-06-01 12:00:00',
                'break2' => '2025-06-01 13:00:00',
                'status' => 'finished',
            ],
            (object)[
                'id' => 2,
                'date' => '2025-06-02',
                'clock_in' => '2025-06-02 09:15:00',
                'clock_out' => null,
                'break1' => null,
                'break2' => null,
                'status' => 'working',
            ],
        ]);

        $currentMonth = '2025-06';
        $prevMonth = '2025-05';
        $nextMonth = '2025-07';

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
        // バリデーションルール
        $validated = $request->validated();

        // 通常はDB更新処理、今回は省略
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

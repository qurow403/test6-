<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 出勤登録画面
    public function create()
    {
        return view('attendance.create'); // resources/views/attendance/create.blade.php を返す
    }

    // 勤怠一覧画面
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
                'status' => 'finished',
            ],
            (object)[
                'id' => 2,
                'date' => '2025-06-02',
                'clock_in' => '2025-06-02 09:15:00',
                'clock_out' => null,
                'status' => 'working',
            ],
        ]);

        $currentMonth = '2025-06';
        $prevMonth = '2025-05';
        $nextMonth = '2025-07';

        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        return view('attendance.index', compact('attendances', 'currentMonth', 'prevMonth', 'nextMonth', 'weekdays'));
    }

    // 勤怠詳細画面
    public function show($id)
    {
        // $attendance = Attendance::findOrFail($id);
        // return view('attendance.show', compact('attendance'));

        // 仮のオブジェクトを用意
        $attendance = (object)[
            'id' => $id,
            'user_id' => 1,
            'date' => '2025-06-11',
            'clock_in' => '2025-06-11 09:00:00',
            'clock_out' => '2025-06-11 18:00:00',
            'status' => 'working',
            'worked_minutes' => 480,
            'break1' => '2025-06-11 12:00:00',
            'break2' => '2025-06-11 15:00:00',
            'note' => 'これは仮の備考です',

            // 👇 ここを追加
            'user' => (object)[
                'name' => 'テストユーザー'
            ],

            // 👇 break を複数配列で持たせる（show.blade.php対応用）
            'breaks' => [
                ['start' => '2025-06-11 12:00:00', 'end' => '2025-06-11 12:30:00'],
                ['start' => '2025-06-11 15:00:00', 'end' => '2025-06-11 15:15:00']
            ]
        ];

        return view('attendance.show', compact('attendance'));
    }

    // 勤怠詳細画面で修正申請するメソッド
    public function update(Request $request, $id)
    {
        // 仮のバリデーションルール
        $validated = $request->validate([
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after_or_equal:clock_in',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i|after_or_equal:breaks.*.start',
            'note' => 'required|string|max:500',
        ]);

        logger()->info("勤怠修正内容（ID: $id）", $validated);

        return redirect()->route('attendance.pending', ['id' => $id])
                        ->with('success', '修正申請を送信しました（承認待ち）')
                        ->with('submitted', $validated);
    }

    // 勤怠詳細画面＿承認待ち
    public function pending($id)
    {
        $submitted = session('submitted'); // セッションから取得

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

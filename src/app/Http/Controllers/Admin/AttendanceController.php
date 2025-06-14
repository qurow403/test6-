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
                'status' => 'left',
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
        $attendance = Attendance::findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }
}

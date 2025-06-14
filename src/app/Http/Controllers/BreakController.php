<?php

namespace App\Http\Controllers;

// Attendanceモデル追加
use App\Models\Attendance;
use Illuminate\Http\Request;

class BreakController extends Controller
{
    public function start(Attendance $attendance)
    {
        if ($attendance->breaks()->whereNull('break_end')->exists()) {
            return back()->with('error', 'すでに休憩中です。');
        }

        $attendance->breaks()->create([
            'break_start' => now(),
        ]);

        $attendance->update(['status' => 'on_break']);

        return back()->with('success', '休憩を開始しました。');
    }

    public function end(Attendance $attendance)
    {
        $break = $attendance->breaks()->whereNull('break_end')->latest()->first();

        if (!$break) {
            return back()->with('error', '休憩中ではありません。');
        }

        $break->update(['break_end' => now()]);
        $attendance->update(['status' => 'working']);

        return back()->with('success', '休憩を終了しました。');
    }
}

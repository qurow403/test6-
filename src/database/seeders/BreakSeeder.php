<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// モデル追加
use App\Models\BreakTime;
use App\Models\Attendance;

// 日付機能
use Carbon\Carbon;

class BreakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Attendance::all() as $attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::today()->setTime(12, 0),
                'break_end' => Carbon::today()->setTime(13, 0),
            ]);
        }
    }
}

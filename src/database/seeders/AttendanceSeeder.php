<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// モデル追加
use App\Models\Attendance;
use App\Models\User;

// 日付機能
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (User::all() as $user) {
            Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::today()->toDateString(),
                'clock_in' => Carbon::today()->setTime(9, 0),
                'clock_out' => Carbon::today()->setTime(18, 0),
            ]);
        }
    }
}

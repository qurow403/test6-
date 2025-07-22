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
        // 任意の6名のユーザーを作成
        $users = User::factory()->count(6)->create();

        foreach ($users as $user) {
            // 勤怠データを1日分作成（例：2023-06-01）
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => '2023-06-01',
            ]);
        }
    }
}

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
        // 固定ユーザーID=1用の勤怠を作成（10日分）
        if (User::find(1)) { // ID=1 のユーザーが存在する場合
            for ($i = 1; $i <= 10; $i++) {
                Attendance::firstOrCreate(
                    [
                        'user_id' => 1,
                        'date' => Carbon::now()->subDays($i)->format('Y-m-d'),
                    ],
                    [
                        'clock_in' => '09:00:00',
                        'clock_out' => '18:00:00',
                        'note' => 'テスト勤怠 #' . $i,
                        'request_status' => 'approved',
                    ]
                );
            }
        }

        // 他のユーザー用（ID=2以降）をファクトリーで1日分作成
        $users = User::factory()->count(5)->create(); // ID=2以降
        foreach ($users as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => '2023-06-01',
            ]);
        }
    }
}

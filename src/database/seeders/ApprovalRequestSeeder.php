<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovalRequest;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApprovalRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ログインユーザーの ID を仮定（例えばユーザー ID 1 の場合）
        $userId = 1;

        // そのユーザーの勤怠データを取得
        $attendances = Attendance::where('user_id', $userId)->get();

        if ($attendances->isEmpty()) {
            $this->command->info("User $userId has no attendance records. Seeder skipped.");
            return;
        }

        // 10 件作成
        for ($i = 1; $i <= 10; $i++) {
            $attendance = $attendances->random(); // 勤怠データをランダムに選択

            ApprovalRequest::create([
                'attendance_id' => $attendance->id,
                'status' => $i % 2 == 0 ? 'approved' : 'pending', // 偶数: approved, 奇数: pending
                'note' => "ダミー申請理由 #$i",
            ]);
        }
    }
}

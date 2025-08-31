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
        // ユーザーID=1の勤怠を取得
        $attendances = Attendance::where('user_id', 1)->get();

        if ($attendances->isEmpty()) {
            $this->command->info("User 1 has no attendance records. Seeder skipped.");
            return;
        }

        // 10件の承認申請を作成
        for ($i = 1; $i <= 10; $i++) {
            $attendance = $attendances->random();

            ApprovalRequest::create([
                'attendance_id' => $attendance->id,
                'status' => $i % 2 === 0 ? 'approved' : 'pending', // 偶数: approved, 奇数: pending
                'note' => "ダミー申請理由 #$i",
            ]);
        }
    }
}

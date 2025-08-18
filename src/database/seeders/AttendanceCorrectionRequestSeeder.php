<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;

class AttendanceCorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 外部キー制約を無効化してから削除
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AttendanceCorrectionRequest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ユーザー取得（例: 先頭5ユーザー）
        $users = User::take(5)->get();

        foreach ($users as $user) {
            // 承認待ち申請を2件ずつ作成
            AttendanceCorrectionRequest::create([
                'user_id' => $user->id,
                'target_date' => Carbon::now()->subDays(rand(1,10)),
                'reason' => '勤務時間の修正依頼',
                'status' => 'pending',  // 承認待ち
                'applied_at' => Carbon::now()->subDays(rand(0,5)),
            ]);

            // 承認済み申請を1件ずつ作成
            AttendanceCorrectionRequest::create([
                'user_id' => $user->id,
                'target_date' => Carbon::now()->subDays(rand(11,20)),
                'reason' => '勤務時間の訂正',
                'status' => 'approved', // 承認済み
                'applied_at' => Carbon::now()->subDays(rand(10,15)),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// モデル追加
use App\Models\AttendanceCorrectionRequest;

class AttendanceCorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AttendanceCorrectionRequest::factory()->count(10)->create();
    }
}

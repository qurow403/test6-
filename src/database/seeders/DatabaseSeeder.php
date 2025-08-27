<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            StaffSeeder::class,
            AttendanceSeeder::class,
            BreakSeeder::class,
            ApprovalRequestSeeder::class,
            AttendanceCorrectionRequestSeeder::class,
        ]);
    }
}

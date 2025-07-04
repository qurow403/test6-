<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// モデル追加
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(5)->create();
    }
}

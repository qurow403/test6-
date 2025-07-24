<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

// モデル・日時情報追加
use App\Models\User;
use Illuminate\Support\Carbon;

class AttendanceCorrectionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'target_date' => Carbon::today()->subDays(rand(1, 10)),
            'reason' => fake()->randomElement(['遅延のため', '記録忘れのため']),
            'status' => fake()->randomElement(['pending', 'approved']),
            'applied_at' => Carbon::now()->subDays(rand(0, 5)),
        ];
    }
}

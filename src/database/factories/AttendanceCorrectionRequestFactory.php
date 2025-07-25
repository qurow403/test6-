<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

// モデル・日時情報追加
use App\Models\User;
use Carbon\Carbon;

class AttendanceCorrectionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();

        return [
            'user_id' => $user ? $user->id : User::factory(),
            'target_date' => Carbon::today()->subDays(rand(1, 10)),
            'reason' => $this->faker->randomElement(['遅延のため', '記録忘れのため']),
            'status' => $this->faker->randomElement(['pending', 'approved']),
            'applied_at' => Carbon::now()->subDays(rand(0, 5)),
        ];
    }
}

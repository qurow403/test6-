<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

// モデル追加
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 month', 'now');
        $clockIn = Carbon\Carbon::instance($date)->setTime(9, 0);
        $clockOut = Carbon\Carbon::instance($date)->setTime(18, 0);
        $breakDuration = 3600; // 1時間
        $workedDuration = $clockOut->diffInSeconds($clockIn) - $breakDuration;

        return [
            'user_id' => User::factory(),
            'date' => $date->format('Y-m-d'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'break_duration' => gmdate('H:i', $breakDuration),
            'worked_duration' => gmdate('H:i', $workedDuration),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;

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
        $clockIn = Carbon::instance($date)->setTime(9, 0);
        $clockOut = Carbon::instance($date)->setTime(18, 0);
        $breakDuration = 60; // 分で保持
        $workedDuration = $clockOut->diffInSeconds($clockIn) - $breakDuration;

        return [
            'user_id' => User::factory(),
            'date' => $date->format('Y-m-d'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'break_duration' => $breakDuration,
            'worked_minutes' => $workedDuration,
        ];
    }
}

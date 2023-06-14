<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currentDate = Carbon::make($this->faker->dateTimeBetween('6/1/2023', '6/30/2023', 'Asia/Manila'));

        return [
            'student_id' => 1,
            'time_in' => $currentDate,
            'time_out' => $currentDate->addHours(6),
            'work_time' => 6,
        ];
    }
}

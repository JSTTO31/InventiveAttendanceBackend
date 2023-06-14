<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AttedanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $table->id();
        // $table->foreignId('student_id');
        // $table->date('time_in')->nullable();
        // $table->date('time_out')->nullable();
        // $table->string('work_time')->nullable();
        // $table->date('is_absent')->default(false);
        // $table->timestamps();
        $currentDate = Carbon::make($this->faker->dateTimeBetween('6/1/2023', '6/30/2023', 'Asia/Manila'));

        return [
            'student_id' => 1,
            'time_in' => $currentDate,
            'time_out' => $currentDate->addHours(6),
            'work_time' => 6,
        ];
    }
}

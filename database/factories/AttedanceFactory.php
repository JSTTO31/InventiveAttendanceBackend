<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AttedanceFactory extends Factory
{
    public static $counter = 1;


    public function definition(): array
    {

        $currentDate = Carbon::today()->addDay(self::$counter)->addHours(9);
        $currentTime = Carbon::today()->addDay(self::$counter)->addHours(18);

        self::$counter++;
        return [
            'student_id' => 1,
            'time_in' => $currentDate,
            'time_out' => $currentTime,
            'work_time' => 6,
        ];
    }
}

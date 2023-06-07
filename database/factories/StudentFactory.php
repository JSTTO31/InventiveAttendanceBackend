<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        $image = $gender == 'male' ? 'http://localhost:8000/storage/profiles/default-male.png' : 'http://localhost:8000/storage/profiles/default-female.png';
        return [
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName($gender),
            'email' => $this->faker->email(),
            'phone_number' => $this->faker->phoneNumber(),
            'school_name' => $this->faker->address(),
            'school_year' => $this->faker->date(),
            'address' => $this->faker->address(),
            'course' => $this->faker->randomElement(['BSCS', 'BSBA', 'BSPS', 'BSBM', 'BSMA']),
            'gender' => $gender,
            'image' => $image

        ];
    }
}

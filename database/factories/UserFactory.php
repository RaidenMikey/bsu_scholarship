<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Random first and last name
        $firstName = $this->faker->firstName();
        $lastName  = $this->faker->lastName();

        return [
            'name'              => "$firstName $lastName",
            'email'             => strtolower($firstName . '.' . $lastName) . '@g.batstate-u.edu.ph',
            'email_verified_at' => now(),
            'password'          => bcrypt('password123'), // default password for all seeded users
            'remember_token'    => Str::random(10),
            'role'              => $this->faker->randomElement(['student', 'sfao', 'central']),
            'branch_id'         => $this->faker->numberBetween(1, 3), // adjust based on how many branches you seeded
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

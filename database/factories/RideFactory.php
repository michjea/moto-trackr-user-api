<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ride;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ride>
 */
class RideFactory extends Factory
{
    protected $model = Ride::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'public' => fake()->boolean(),
            'distance' => fake()->randomFloat(2, 0, 1000),
            'duration' => fake()->randomFloat(2, 0, 1000),
            'max_speed' => fake()->randomFloat(2, 0, 1000),
            'avg_speed' => fake()->randomFloat(2, 0, 1000),
            'positions' => fake()->randomFloat(2, 0, 1000),
            'route' => fake()->randomFloat(2, 0, 1000),
        ];
    }
}

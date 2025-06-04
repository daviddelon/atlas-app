<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observation>
 */
class ObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'observation_id' => fake()->randomNumber(),
            'taxon_id' => fake()->randomNumber(),
            'observed_on' => fake()->date(),
            'observed_by' => fake()->randomNumber(),
            'license' => fake()->word(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];

    }
}

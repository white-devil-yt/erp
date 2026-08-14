<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'source' => fake()->randomElement(array_keys(Lead::SOURCES)),
            'status' => fake()->randomElement(array_keys(Lead::STATUSES)),
            'value' => fake()->randomFloat(2, 1000, 100000),
            'expected_close_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'notes' => fake()->sentence(),
        ];
    }
}

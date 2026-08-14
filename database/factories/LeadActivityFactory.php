<?php

namespace Database\Factories;

use App\Models\LeadActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadActivity>
 */
class LeadActivityFactory extends Factory
{
    protected $model = LeadActivity::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(array_keys(LeadActivity::TYPES)),
            'note' => fake()->sentence(),
            'next_follow_up' => fake()->optional()->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
        ];
    }
}

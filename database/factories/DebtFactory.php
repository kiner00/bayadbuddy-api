<?php

namespace Database\Factories;

use App\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'borrower_id' => Borrower::factory(), // Auto-create borrower if none passed
            'amount' => $this->faker->randomFloat(2, 100, 10000), // Between 100 and 10k
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'status' => 'pending', // Default pending
            'interest_type' => 'none', // Or later randomize if you want
            'interest_value' => null,
        ];
    }
}

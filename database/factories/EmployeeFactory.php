<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'office' => fake()->city(),
            'age' => fake()->numberBetween(22, 65),
            'start_date' => fake()->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
            'salary' => fake()->randomFloat(2, 40_000, 250_000),
        ];
    }
}

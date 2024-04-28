<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_id' => Company::inRandomOrder()->first()->id,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber()
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory; // Importação correta
use App\Models\User;
use App\Models\Team;
use App\Models\Category;
use App\Models\Expense;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'user_id' => User::factory(), // Cria automaticamente um usuário válido
            'team_id' => Team::factory(), // Cria automaticamente uma equipe válida
            'category_id' => Category::factory(), // Cria automaticamente uma categoria válida
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'frequency' => $this->faker->randomElement(['one-time', 'monthly', 'annually']),
            'type' => $this->faker->randomElement(['fixed', 'reimbursable']),
            'status' => 'pending',
        ];
    }
}

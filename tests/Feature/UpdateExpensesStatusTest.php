<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Team;
use App\Models\Category;
use App\Jobs\UpdateExpensesStatus;

class UpdateExpensesStatusTest extends TestCase
{
    public function test_overdue_expenses_are_marked_and_notified()
    {
        // Criar um usuário válido
        $user = User::factory()->create();

        // Criar uma equipe válida
        $team = Team::factory()->create([
            'user_id' => $user->id,
        ]);

        // Criar uma categoria válida (se necessário)
        $category = Category::factory()->create();

        // Criar despesa com dependências
        $expense = Expense::factory()->create([
            'user_id' => 1,
            'team_id' => $team->id,
            'category_id' => $category->id,
            'payment_date' => now()->subDays(5), // Data no passado
            'status' => 'pending',
        ]);

        // Executar o job para atualizar o status
        UpdateExpensesStatus::dispatchSync();

        // Verificar se o status foi atualizado para "overdue"
        $this->assertEquals('overdue', $expense->refresh()->status);

        // Verificar se a notificação foi enviada
        $this->assertNotNull($expense->notified_at);
    }

}

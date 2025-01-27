<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateRecurringExpenses extends Command
{
    protected $signature = 'expenses:generate-recurring';
    protected $description = 'Generate recurring expenses for active records';

    public function handle()
    {
        $today = Carbon::today(); // Data atual

        // Verifica se é o último dia do mês
        if (!$today->isLastOfMonth()) {
            $this->info('Today is not the last day of the month. Skipping execution.');
            return;
        }

        // Busca todas as despesas ativas com frequência mensal ou anual
        $expenses = Expense::where('is_active', true)
            ->whereIn('frequency', ['monthly', 'annually'])
            ->get();

        foreach ($expenses as $expense) {
            // Gera a próxima despesa
            $nextExpense = $expense->generateNextExpense();

            if ($nextExpense) {
                $this->info("Next expense generated for: {$expense->title}");
            } else {
                $this->info("Skipped duplicate for: {$expense->title}");
            }
        }

        $this->info('Recurring expenses processed successfully.');
    }
}

<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Expense;
use App\Notifications\ExpenseOverdueNotification;

class UpdateExpensesStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $expenses = Expense::where('status', 'pending')
            ->where('payment_date', '<', now())
            ->get();

        foreach ($expenses as $expense) {
            // Atualizar status para overdue
            $expense->updateStatus();

            // Notificar o usuário uma vez
            if ($expense->status === 'overdue' && is_null($expense->notified_at)) {
                $expense->user->notify(new ExpenseOverdueNotification($expense));
                $expense->notified_at = now();
                $expense->save();
            }
        }
    }

}

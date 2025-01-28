<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Team;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Notifications\ExpenseOverdueNotification;

class ExpenseController extends Controller
{
    /**
     * Lista as despesas com filtros opcionais.
     */
    public function index(Request $request)
    {
        $teamId = Auth::user()->currentTeam->id;

        $query = Expense::query()->where('team_id', $teamId);

        // Definir mês e ano atual se não houver parâmetros
        $currentMonthYear = Carbon::now();
        $monthYear = $request->monthYear ?? $currentMonthYear->format('Y-m');

        // Filtrar por mês e ano
        [$year, $month] = explode('-', $monthYear);
        $query->whereYear('payment_date', $year)->whereMonth('payment_date', $month);

        // Filtrar por título (insensível a maiúsculas/minúsculas)
        if ($request->search) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $expenses = $query->get();

        return inertia('Expenses/Index', [
            'expenses' => $expenses,
            'isAdmin' => Auth::user()->isAdmin(),
            'query' => ['monthYear' => $monthYear, 'search' => $request->search],
        ]);
    }

    public function reports(Request $request)
{
    $teamId = Auth::user()->currentTeam->id;

    // Filtros de mês/ano e título
    $currentMonthYear = Carbon::now()->format('Y-m');
    $monthYear = $request->input('monthYear', $currentMonthYear);

    try {
        $parsedDate = Carbon::createFromFormat('Y-m', $monthYear);
        $year = $parsedDate->year;
        $month = $parsedDate->month;
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['monthYear' => 'Invalid month and year format.']);
    }

    // Filtrar despesas por mês/ano e título
    $query = Expense::query()->where('team_id', $teamId);
    $query->whereYear('payment_date', $year)->whereMonth('payment_date', $month);

    if ($request->input('search')) {
        $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
    }

    // Dados para os cards e a tabela
    $expenses = $query->get();
    $summary = [
        'total' => $query->sum('amount'),
        'paid' => (clone $query)->where('status', 'paid')->sum('amount'),
        'pending' => (clone $query)->where('status', 'pending')->sum('amount'),
        'overdue' => (clone $query)->where('status', 'overdue')->sum('amount'),
    ];
    
    // Dados do gráfico (independentes do filtro de mês/ano)
    $monthlyStats = Expense::select(
        DB::raw("strftime('%Y-%m', payment_date) as month"),
        DB::raw('SUM(amount) as total')
    )
        ->where('team_id', $teamId)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    return inertia('Expenses/Reports', [
        'expenses' => $expenses,
        'query' => [
            'monthYear' => $monthYear,
            'search' => $request->input('search', ''),
        ],
        'summary' => $summary,
        'monthlyStats' => $monthlyStats,
    ]);
}


    /**
     * Exibe dados analíticos de despesas.
     */
    public function analytics()
    {
        $teamId = Auth::user()->currentTeam->id;

        $totalExpenses = Expense::where('team_id', $teamId)->sum('amount');

        $expensesByCategory = Expense::join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select('categories.name as name', DB::raw('SUM(expenses.amount) as total'))
            ->where('expenses.team_id', $teamId)
            ->groupBy('categories.name')
            ->get();

        $monthlyExpenses = Expense::select(
            DB::raw("strftime('%m', payment_date) as month"),
            DB::raw('SUM(amount) as total')
        )
            ->where('team_id', $teamId)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($expense) {
                return [
                    'month' => Carbon::createFromFormat('m', $expense->month)->format('F'),
                    'total' => $expense->total,
                ];
            });

        return inertia('Expenses/Analytics', [
            'totalExpenses' => $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'monthlyExpenses' => $monthlyExpenses,
        ]);
    }

    /**
     * Exibe o formulário de criação de uma despesa.
     */
    public function create()
    {
        $categories = Category::all();

        return inertia('Expenses/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Armazena uma nova despesa.
     */
    /**
     * Armazena uma nova despesa.
     */
    public function store(Request $request)
    {
        $validated = $this->validateExpense($request);

        // Gerenciar upload de recibo
        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['team_id'] = Auth::user()->currentTeam->id;

        // Criar apenas a despesa inicial
        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }


    /**
     * Exibe o formulário de edição de uma despesa.
     */
    public function edit(Expense $expense)
    {
        $expense = Expense::findOrFail($expense->id);

        return inertia('Expenses/Edit', [
            'expense' => $expense, // Somente a despesa será enviada.
        ]);
    }

    /**
     * Atualiza uma despesa existente.
     */
    public function update(Request $request, Expense $expense)
    {
        // Validação dos campos
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'frequency' => 'required|in:one-time,monthly,annually',
            'type' => 'required|in:fixed,reimbursable',
            'status' => 'required|in:pending,paid,overdue', // Validar o status
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpeg,png,pdf|max:5120', // Opcional
        ]);

        // Gerenciar upload do recibo
        if ($request->hasFile('receipt')) {
            // Deletar o recibo antigo, se existir
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        // Atualizar a despesa
        $expense->update($validated);

        // Notificar o usuário, se o status for alterado para "overdue"
        if ($expense->wasChanged('status') && $validated['status'] === 'overdue') {
            $expense->user->notify(new ExpenseOverdueNotification($expense));
        }

        return redirect()->back()->banner('Expense updated successfully.');
    }

    public function show(Expense $expense)
    {
        $expense = Expense::findOrFail($expense->id);

        return inertia('Expenses/Show', [
            'expense' => $expense, // Somente a despesa será enviada.
        ]);
    }

    /**
     * Remove uma despesa.
     */
    public function destroy(Expense $expense)
    {
        // Deletar recibo associado, se existir
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    /**
     * Valida os dados da despesa.
     */
    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'payment_date' => 'nullable|date',
            'frequency' => 'required|in:one-time,monthly,annually',
            'type' => 'required|in:fixed,reimbursable',
            'receipt' => 'nullable|file|mimes:jpeg,png,pdf|max:5120', // Recibo (opcional)
            'notes' => 'nullable|string',
        ]);
    }


    /**
     * Marca uma despesa como paga.
     */
    public function markAsPaid(Expense $expense)
    {
        // Atualiza o status da despesa para "paid"
        $expense->update([
            'status' => 'paid',
            'payment_date' => Carbon::now(), // Define a data de pagamento como hoje
        ]);

        return redirect()->back()->banner('Expense marked as paid successfully.');
    }


    /**
     * Agenda a próxima despesa recorrente.
     */
    protected function scheduleNextExpense(Expense $expense)
    {
        $nextPaymentDate = match ($expense->frequency) {
            'monthly' => Carbon::parse($expense->payment_date)->addMonth()->toDateString(),
            'annually' => Carbon::parse($expense->payment_date)->addYear()->toDateString(),
            default => null,
        };

        if ($nextPaymentDate) {
            Expense::create([
                'title' => $expense->title,
                'description' => $expense->description,
                'amount' => $expense->amount,
                'category_id' => $expense->category_id,
                'user_id' => $expense->user_id,
                'team_id' => $expense->team_id,
                'payment_date' => $nextPaymentDate,
                'frequency' => $expense->frequency,
                'receipt_path' => $expense->receipt_path,
                'type' => $expense->type,
                'notes' => $expense->notes,
            ]);
        }
    }
}

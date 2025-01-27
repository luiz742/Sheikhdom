<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'user_id',
        'team_id',
        'category_id',
        'payment_date',
        'frequency',
        'receipt_path', // Caminho do recibo
        'type',         // Tipo da despesa
        'notes',
        'status',
        'notified_at'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Gera a próxima despesa baseada na frequência.
     *
     * @return Expense|null Retorna a nova despesa criada ou null.
     */
    public function generateNextExpense()
    {
        // Verifica se a despesa está ativa e possui frequência válida
        if (!$this->is_active || !$this->payment_date || !in_array($this->frequency, ['monthly', 'annually'])) {
            return null;
        }

        // Calcula a próxima data de pagamento no último dia do mês
        $nextDate = match ($this->frequency) {
            'monthly' => Carbon::parse($this->payment_date)->addMonth()->lastOfMonth(),
            'annually' => Carbon::parse($this->payment_date)->addYear()->lastOfMonth(),
        };

        // Verifica se já existe uma despesa com a mesma data de pagamento
        $exists = self::where('title', $this->title)
            ->where('team_id', $this->team_id)
            ->where('payment_date', $nextDate)
            ->exists();

        if ($exists) {
            return null; // Evita duplicação
        }

        // Cria a nova despesa
        return self::create([
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'category_id' => $this->category_id,
            'payment_date' => $nextDate,
            'frequency' => $this->frequency,
            'receipt_path' => $this->receipt_path,
            'type' => $this->type,
            'notes' => $this->notes,
            'is_active' => true,
        ]);
    }

    public function updateStatus()
    {
        if ($this->status === 'paid') {
            return; // Se já está pago, não faz nada
        }

        $now = Carbon::now();

        if ($this->payment_date < $now && $this->status !== 'overdue') {
            $this->status = 'overdue';
            $this->save();
        }
    }
}

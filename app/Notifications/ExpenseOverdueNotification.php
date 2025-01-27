<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseOverdueNotification extends Notification
{
    use Queueable;

    protected $expense; // Propriedade para armazenar a despesa

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Expense  $expense
     */
    public function __construct($expense)
    {
        $this->expense = $expense;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $paymentDate = \Carbon\Carbon::parse($this->expense->payment_date)->format('F j, Y'); // Formata a data de pagamento

        return (new MailMessage)
            ->subject('Overdue Expense Notification')
            ->greeting('Hello!')
            ->line('The expense "' . $this->expense->title . '" is overdue.')
            ->line('**Team:** ' . $this->expense->team->name) // Exibe o nome do time
            ->line('**Payment Date:** ' . $paymentDate) // Exibe a data de pagamento
            ->action('View Expense', url('/expenses/' . $this->expense->id))
            ->line('Please take action as soon as possible.')
            ->salutation('Regards, Sheikhdom');
    }
}

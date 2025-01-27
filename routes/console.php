<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Jobs\UpdateExpensesStatus;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure-based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Agendamento do comando de gerar despesas recorrentes
app(Schedule::class)->command('expenses:generate-recurring')->monthlyOn(Carbon::now()->lastOfMonth()->day, '00:00');

// Agendamento do job para atualizar o status das despesas
app(Schedule::class)->job(new UpdateExpensesStatus)->everyMinute();

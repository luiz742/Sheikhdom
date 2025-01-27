<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDateAndFrequencyToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->date('payment_date')->nullable(); // Data do pagamento, pode ser nula
            $table->enum('frequency', ['one-time', 'monthly', 'annually'])->default('one-time'); // Frequência da despesa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('payment_date');
            $table->dropColumn('frequency');
        });
    }
}

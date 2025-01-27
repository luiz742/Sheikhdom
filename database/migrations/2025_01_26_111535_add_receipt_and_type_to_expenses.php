<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('amount'); // Caminho do recibo
            $table->enum('type', ['fixed', 'reimbursable'])->default('fixed')->after('receipt_path'); // Tipo da despesa
            $table->text('notes')->nullable()->after('type'); // Notas adicionais
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['receipt_path', 'type', 'notes']);
        });
    }

};

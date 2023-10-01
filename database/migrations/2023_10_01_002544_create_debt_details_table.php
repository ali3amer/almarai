<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debt_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_debt_id');
            $table->foreign('client_debt_id')->references('id')->on('client_debts');
            $table->decimal('remainder', 8, 2);
            $table->enum('payment', ['cash', 'bank']);
            $table->string('bank')->nullable();
            $table->decimal('paid', 8, 2);
            $table->decimal('client_balance', 8, 2)->nullable();
            $table->date('due_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_details');
    }
};

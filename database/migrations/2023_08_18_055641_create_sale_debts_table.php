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
        Schema::create('sale_debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->decimal('remainder', 8, 2);
            $table->enum('payment', ['cash', 'bank']);
            $table->string('bank')->nullable();
            $table->decimal('paid', 8, 2);
            $table->decimal('discount', 8, 2)->nullable();
            $table->decimal('client_balance', 8, 2)->nullable();
            $table->date('due_date');
            $table->unsignedBigInteger('gift_id')->nullable();
            $table->foreign('gift_id')->references('id')->on('employee_gifts');
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
        Schema::dropIfExists('sale_debts');
    }
};

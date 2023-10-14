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
        Schema::create('supplier_debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
           $table->unsignedBigInteger('purchase_id')->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->enum('type', ['debt', 'pay']);
            $table->decimal('debt', 8, 2);
            $table->decimal('paid', 8, 2);
            $table->enum('payment', ['cash', 'bank']);
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->string('note')->nullable();
            $table->string('bank')->nullable();
            $table->date('due_date');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->foreign('sale_id')->references('id')->on('sales');
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
        Schema::dropIfExists('supplier_debts');
    }
};

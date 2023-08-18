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
        Schema::create('purchase_debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->decimal('credit_amount', 8, 2);
            $table->date('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_debts');
    }
};

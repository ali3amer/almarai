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
        Schema::create('deposit_debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id')->nullable();
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('type', ['debt', 'pay']);
            $table->decimal('amount', 10, 2);
            $table->enum('payment', ['cash', 'bank'])->default("cash");
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bank')->nullable();
            $table->string('note')->nullable();
            $table->date('due_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_debts');
    }
};

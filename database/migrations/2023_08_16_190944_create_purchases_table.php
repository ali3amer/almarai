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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('people_id')->nullable();
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('payment', ['cash', 'bank', 'parts'])->default("cash");
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bank')->nullable();
            $table->string('note')->nullable();
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('bank_paid', 10, 2)->default(0);
            $table->decimal('remainder', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

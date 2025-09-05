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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('to_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('transaction_hash')->nullable();
            $table->string('block')->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->string('token')->nullable();

            $table->decimal('amount', 32, 8)->nullable();
            $table->decimal('amount_usd', 32, 8)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

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
        Schema::create('barter_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barter_acquirer_id');
            $table->unsignedBigInteger('barter_transaction_id');
            $table->foreign('barter_acquirer_id')->references('id')->on('users');
            $table->foreign('barter_transaction_id')->references('id')->on('barter_transactions');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_invoices');
    }
};

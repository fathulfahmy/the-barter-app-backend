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
            $table->decimal('amount', 10, 2)->default(0);

            $table->unsignedBigInteger('barter_acquirer_id');
            $table->foreign('barter_acquirer_id')->references('id')->on('users');

            $table->unsignedBigInteger('barter_transaction_id');
            $table->foreign('barter_transaction_id')->references('id')->on('barter_transactions');

            $table->timestamps();
            $table->softDeletes();
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

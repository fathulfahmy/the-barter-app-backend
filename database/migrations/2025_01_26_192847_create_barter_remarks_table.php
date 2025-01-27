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
        Schema::create('barter_remarks', function (Blueprint $table) {
            $table->id();
            $table->datetime('datetime')->nullable();
            $table->text('address')->nullable();
            $table->text('deliverables')->nullable();
            $table->text('note')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('barter_remarks');
    }
};

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
        Schema::create('barter_transactions', function (Blueprint $table) {
            $table->id();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'awaiting_completed', 'completed', 'cancelled'])->default('pending');

            $table->unsignedBigInteger('barter_acquirer_id');
            $table->foreign('barter_acquirer_id')->references('id')->on('users');

            $table->unsignedBigInteger('barter_provider_id');
            $table->foreign('barter_provider_id')->references('id')->on('users');

            $table->unsignedBigInteger('awaiting_user_id')->nullable();
            $table->foreign('awaiting_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('barter_service_id');
            $table->foreign('barter_service_id')->references('id')->on('barter_services');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_transactions');
    }
};

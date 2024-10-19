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
            $table->unsignedBigInteger('barter_acquirer_id');
            $table->unsignedBigInteger('barter_provider_id');
            $table->unsignedBigInteger('barter_service_id');
            $table->foreign('barter_acquirer_id')->references('id')->on('users');
            $table->foreign('barter_provider_id')->references('id')->on('users');
            $table->foreign('barter_service_id')->references('id')->on('barter_services');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
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
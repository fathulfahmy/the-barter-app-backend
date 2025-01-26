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
        Schema::create('barter_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->integer('rating')->default(1);

            $table->unsignedBigInteger('reviewer_id');
            $table->foreign('reviewer_id')->references('id')->on('users');

            $table->unsignedBigInteger('barter_service_id')->nullable();
            $table->foreign('barter_service_id')->references('id')->on('barter_services');

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
        Schema::dropIfExists('barter_reviews');
    }
};

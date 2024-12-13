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
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('barter_service_id');
            $table->unsignedBigInteger('barter_transaction_id');
            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('barter_service_id')->references('id')->on('barter_services');
            $table->foreign('barter_transaction_id')->references('id')->on('barter_transactions');
            $table->text('description');
            $table->decimal('rating', 2, 1)->default(0);
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barter_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barter_provider_id');
            $table->unsignedBigInteger('barter_category_id');
            $table->foreign('barter_provider_id')->references('id')->on('users');
            $table->foreign('barter_category_id')->references('id')->on('barter_categories');
            $table->string('title');
            $table->text('description');
            $table->string('price');
            $table->decimal('rating', 2, 1)->default('0');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_services');
    }
};

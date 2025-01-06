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
        Schema::create('barter_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('min_price', 10, 2)->default('0');
            $table->decimal('max_price', 10, 2)->default('0');
            $table->string('price_unit');
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');

            $table->unsignedBigInteger('barter_provider_id');
            $table->foreign('barter_provider_id')->references('id')->on('users');

            $table->unsignedBigInteger('barter_category_id');
            $table->foreign('barter_category_id')->references('id')->on('barter_categories');

            $table->timestamps();
            $table->softDeletes();
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

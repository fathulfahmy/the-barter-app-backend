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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('reporter_id');
            $table->foreign('reporter_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_report_reason_id');
            $table->foreign('user_report_reason_id')->references('id')->on('user_report_reasons');

            $table->unsignedBigInteger('model_id');
            $table->enum('model_name', ['user', 'barter_service', 'barter_transaction', 'barter_invoice', 'barter_review']);

            $table->enum('status', ['unread', 'read'])->default('unread');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports per user');
    }
};

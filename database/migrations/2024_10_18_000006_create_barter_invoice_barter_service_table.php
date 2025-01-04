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
        Schema::create('barter_invoice_barter_service', function (Blueprint $table) {
            $table->unsignedBigInteger('barter_invoice_id');
            $table->foreign('barter_invoice_id')->references('id')->on('barter_invoices');

            $table->unsignedBigInteger('barter_service_id');
            $table->foreign('barter_service_id')->references('id')->on('barter_services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_invoice_barter_service');
    }
};

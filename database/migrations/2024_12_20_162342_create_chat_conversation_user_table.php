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
        Schema::create('chat_conversation_user', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_conversation_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('chat_conversation_id')->references('id')->on('chat_conversations');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversation_user');
    }
};

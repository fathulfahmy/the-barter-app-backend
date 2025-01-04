<?php

use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-conversation.{chat_conversation_id}', function (User $user, ChatConversation $chat_conversation_id) {
    return ChatConversation::query()
        ->where('id', $chat_conversation_id)
        ->whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->exists();
});

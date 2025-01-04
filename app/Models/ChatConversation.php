<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatMessage> $chat_messages
 * @property-read int|null $chat_messages_count
 * @property-read \App\Models\ChatConversationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\ChatConversationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereUpdatedAt($value)
 */
class ChatConversation extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ChatConversationFactory> */
    use HasFactory;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(ChatConversationUser::class);
    }

    public function chat_messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_conversation_id');
    }

    public function latest_message(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}

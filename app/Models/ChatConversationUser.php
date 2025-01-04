<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $chat_conversation_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser whereChatConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversationUser whereUserId($value)
 */
class ChatConversationUser extends Pivot
{
    //
}

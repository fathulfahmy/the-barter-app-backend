<?php

namespace App\Console\Commands;

use App\Events\ChatMessageSent;
use App\Models\ChatConversation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class SendChatMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:send-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $conversation_id = text(
                label: 'Chat conversation ID',
                required: true,
            );

            $author_id = text(
                label: 'Author ID',
                required: true,
            );

            $content = textarea(
                label: 'Write a message',
                required: true,
            );

            Auth::loginUsingId($author_id);

            $chat_conversation = ChatConversation::findOrFail($conversation_id);

            $chat_message = $chat_conversation->chat_messages()->create([
                'author_id' => $author_id,
                'content' => $content,
            ]);

            broadcast(new ChatMessageSent($chat_conversation, $chat_message))->toOthers();

            DB::commit();

            $this->info('Message sent');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('Failed to send message');
            $this->error($e->getMessage());
        }
    }
}

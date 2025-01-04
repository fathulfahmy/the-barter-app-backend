<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Http\Requests\ChatMessageStoreRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Chat
 */
class ChatMessageController extends BaseController
{
    /**
     * Get Messages
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<ChatMessage>,
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $chat_conversation_id = $request->input('chat_conversation_id');
            $chat_messages = ChatMessage::query()
                ->where('chat_conversation_id', $chat_conversation_id)
                ->paginate(config('app.default.pagination'));

            return response()->apiSuccess('Messages fetched successfully', $chat_messages);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to fetch messages', $e->getMessage());
        }
    }

    /**
     * Create Message
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: ChatMessage,
     * }
     */
    public function store(ChatMessageStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $chat_conversation = ChatConversation::findOrFail($validated['chat_conversation_id']);

            $chat_message = $chat_conversation->chat_messages()->create([
                'author_id' => auth()->id(),
                'content' => $validated['content'],
            ]);

            broadcast(new ChatMessageSent($chat_conversation, $chat_message))->toOthers();

            DB::commit();

            return response()->apiSuccess('Message sent successfully', $chat_message, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to send message', $e->getMessage());
        }
    }
}

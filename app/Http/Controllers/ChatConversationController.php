<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatConversationStoreRequest;
use App\Models\ChatConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Chat
 */
class ChatConversationController extends BaseController
{
    /**
     * Get Conversations
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<ChatConversation>,
     * }
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $chat_conversations = $user->chat_conversations()
                ->with([
                    'users' => function ($query) use ($user) {
                        $query->whereNot('users.id', $user->id);
                    },
                    'latest_message',
                ])
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->paginate(config('app.default.pagination'));

            $chat_conversations->getCollection()->transform(function ($chat_conversation) {
                $latest_message = $chat_conversation->latest_message;
                if (! empty($latest_message)) {
                    $latest_message->created_at_diff = $this->formatTimeDiff($latest_message->created_at);
                }

                return $chat_conversation;
            });

            return response()->apiSuccess('Conversations fetched successfully', $chat_conversations);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to fetch conversations', $e->getMessage());
        }
    }

    /**
     * Get Conversation
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Conversation
     * }
     */
    public function show(string $chat_conversation_id): JsonResponse
    {
        try {
            $user = auth()->user();
            $chat_conversation = ChatConversation::query()
                ->with([
                    'users' => function ($query) use ($user) {
                        $query->whereNot('users.id', $user->id);
                    },
                ])
                ->findOrFail($chat_conversation_id);

            return response()->apiSuccess('Conversation fetched successfully', $chat_conversation);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to fetch conversation', $e->getMessage());
        }
    }

    /**
     * Create Conversation
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: ChatConversation,
     * }
     */
    public function store(ChatConversationStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $chat_conversation = ChatConversation::create();

            array_push($validated['user_ids'], auth()->id());
            $chat_conversation->users()->attach($validated['user_ids']);

            DB::commit();

            return response()->apiSuccess('Conversation created successfully', $chat_conversation, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create conversation', $e->getMessage());
        }
    }
}

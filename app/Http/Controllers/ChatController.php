<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Services\ChatService;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ChatController extends Controller
{
    use HasApiResponse;

    public function __construct(protected ChatService $chatService)
    {
    }

    public function index(Request $request)
    {
        $chats = $this->chatService->index($request);
        return $this->success(['chats' => ChatResource::collection($chats)]);
    }

    public function sendMessage(SendMessageRequest $request)
    {
        $chat = $this->chatService->sendMessage($request);

        return $this->success(data: ['chat' => ChatResource::make($chat)], message: 'Message sent successfully');
    }

    public function show(Chat $chat)
    {
        $this->chatService->markMessagesAsRead($chat, auth()->user());
        $messages = $chat->messages()->orderBy('sent_at')->get();
        return $this->success(MessageResource::collection($messages));
    }

    public function streamChats(Request $request)
    {

        $this->authenticateViaToken($request);
        return response()->stream(function () {
            $start = microtime(true);
            $timeout = 1.5;

            $newChats = app(ChatService::class)->watchAllChatsSimple();


            if ($newChats->isNotEmpty()) {
                echo "event: chat\n";
                echo "data: " . json_encode($newChats) . "\n\n";
                ob_flush();
                flush();
            }

            if ((microtime(true) - $start) >= $timeout) {
                echo "event: close\n";
                echo "data: {}\n\n";
                ob_flush();
                flush();
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public function streamChatMessages(Request $request, Chat $chat)
    {
        $this->authenticateViaToken($request);
        $lastMessageId = (int) $request->query('last_message_id', 0);

        return response()->stream(function () use ($chat, $lastMessageId) {
            $start = microtime(true);
            $timeout = 1.5;

            $messages = app(ChatService::class)->watchChatMessages($chat, $lastMessageId);

            if ($messages->isNotEmpty()) {
                echo "event: message\n";
                echo "data: " . $messages->toJson() . "\n\n";
                ob_flush();
                flush();
            }

            if ((microtime(true) - $start) >= $timeout) {
                echo "event: close\n";
                echo "data: {}\n\n";
                ob_flush();
                flush();
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    protected function authenticateViaToken(Request $request): void
    {
        $token = $request->query('token');
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                Auth::login($accessToken->tokenable);
            } else {
                abort(401, 'Unauthorized: Invalid token');
            }
        } else {
            abort(401, 'Unauthorized: Token is required');
        }
    }

    public function block(Chat $chat)
    {
        $this->chatService->blockChat($chat);
        return $this->success(message: 'Chat blocked successfully');
    }

    public function unblock(Chat $chat)
    {
        $this->chatService->unblockChat($chat);
        return $this->success(message: 'Chat unblocked successfully');
    }
}

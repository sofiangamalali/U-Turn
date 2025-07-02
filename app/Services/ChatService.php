<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Listing;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatService
{
    public function index($request)
    {
        $userId = auth()->id();
        $type = $request->query('type');

        $query = Chat::query()
            ->with(['listing', 'messages', 'firstMessage', 'lastMessage'])
            ->withCount([
                'unreadMessages as unread_counts'
            ]);

        if ($type === 'buy') {
            $query->buying($userId);
        } elseif ($type === 'sell') {
            $query->selling($userId);
        } else {
            $query->forUser($userId);
        }

        return $query->get();
    }

    public function sendMessage($request)
    {
        $chat = null;

        DB::transaction(function () use ($request, &$chat) {
            $user = auth()->user();
            $data = $request->validated();

            if (!empty($data['chat_id'])) {
                $chat = Chat::findOrFail($data['chat_id']);

                $firstMessage = $chat->firstMessage;
                $receiverId = $firstMessage->sender_id === $user->id
                    ? $firstMessage->receiver_id
                    : $firstMessage->sender_id;

                if ($receiverId === $user->id) {
                    throw new \Exception("لا يمكنك إرسال رسالة إلى نفسك.");
                }

            } else {
                $listing = Listing::findOrFail($data['listing_id']);
                $receiverId = $listing->user_id;

                if ($receiverId === $user->id) {
                    throw new \Exception("لا يمكنك بدء محادثة مع إعلانك.");
                }

                $chat = Chat::where('listing_id', $listing->id)
                    ->whereHas('messages', function ($q) use ($user, $receiverId) {
                        $q->where(function ($q2) use ($user, $receiverId) {
                            $q2->where('sender_id', $user->id)
                                ->where('receiver_id', $receiverId);
                        })->orWhere(function ($q2) use ($user, $receiverId) {
                            $q2->where('sender_id', $receiverId)
                                ->where('receiver_id', $user->id);
                        });
                    })->first();

                if (!$chat) {
                    $chat = Chat::create([
                        'listing_id' => $listing->id,
                        'name' => $listing->title,
                        'blocked' => false,
                    ]);
                }
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'type' => $data['type'],
                'message' => $data['message'] ?? null,
                'sent_at' => now(),
            ]);

            if ($data['type'] === 'image' && $request->hasFile('image')) {
                $message->uploadImage($request->file('image'));
            }

            if ($data['type'] === 'voice' && $request->hasFile('voice')) {
                $message->uploadVoice($request->file('voice'));
            }

        });

        return $chat;
    }

    public function markMessagesAsRead(Chat $chat, $user): void
    {
        $chat->messages()
            ->where('receiver_id', $user->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function watchAllChatsSimple()
    {
        return Chat::query()
            ->with('listing')
            ->whereHas('messages', function ($q) {
                $q->where('receiver_id', auth()->id());
            })
            ->latest('updated_at')
            ->get();
    }

    public function watchChatMessages(Chat $chat, $lastMessageId)
    {
        return $chat->messages()
            ->where('receiver_id', auth()->id())
            ->where('id', '>', $lastMessageId)
            ->orderBy('id')
            ->get();
    }

    public function blockChat(Chat $chat): void
    {
        $chat->update(['blocked' => true]);
    }

    public function unblockChat(Chat $chat): void
    {
        $chat->update(['blocked' => false]);
    }
}

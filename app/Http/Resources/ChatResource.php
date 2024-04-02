<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ChatMessage;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'messages' => ChatMessageResource::collection($this->getMessages($this->id, $this->unique_id))
        ];
    }

    private function getMessages($chat_id, $unique_id){
        $chatMessages = ChatMessage::query()
            ->select('chat_messages.*', 'users.first_name')
            ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
            ->join('users', 'chats.user_id', '=', 'users.id')
            ->where('chat_messages.unique_id', '=', $unique_id)
            ->where('chat_messages.deleted_flag', '=', 0)
            ->orderBy('chat_messages.created_at')
            ->get();

        $today = date('Y-m-d');

        foreach ($chatMessages as $item) {
            $timestamp = strtotime($item->created_at);
            $messageDate = date('Y-m-d', $timestamp);

            $item['time'] = date('h:i', $timestamp);
            $item['position'] = $item->chat_id != $chat_id ? 'right' : '';
            $item['label'] = $messageDate == $today ? 'today' : date('Y-m-d', $timestamp);
            $item['name'] = $item->first_name;

        }

        return $chatMessages;
    }
}

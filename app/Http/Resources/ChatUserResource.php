<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatUserResource extends JsonResource
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
            'role_id' => $this->role_id,
            'role_upper' => ucfirst($this->role->role),
            'role_lower' => $this->role->role,
            'first_name' => $this->first_name,
            'name' => $this->first_name.' '.$this->last_name,
            'online_status' => $this->checkOnline($this->last_login),
            // 'online_statu' => $this->getOnline($this->last_login),
            'image' => $this->image_url ? URL::to($this->image_url) : URL::to('defaults/def-avatar.png'),
            'recent_message' => $this->getCurrentMessage($this->id)
        ];
    }

    private function checkOnline($last_login){
        // Get the current time in the 'Asia/Manila' timezone
        $manilaTimezone = 'Asia/Manila';
        $nowInManila = Carbon::now($manilaTimezone);

        // Parse the last login time as a Carbon instance
        $lastLoginTime = Carbon::parse($last_login, $manilaTimezone);

        // Check if the user is online based on a specific timeframe, e.g., 5 minutes
        //$last_login > Carbon::now()->subSeconds(500)->format('Y-m-d H:i:s');
        $manilaTime = Carbon::parse($nowInManila)->subSeconds(5)->format('Y-m-d H:i:s');
        $lastTime = Carbon::parse($last_login)->format('Y-m-d H:i:s');
        $online = $lastTime > $manilaTime;
        // Return the result (true if online, false if not)
        return $online;
    }
    // private function getOnline($last_login){
    //     // Get the current time in the 'Asia/Manila' timezone
    //     $manilaTimezone = 'Asia/Manila';
    //     $nowInManila = Carbon::now($manilaTimezone);

    //     // Parse the last login time as a Carbon instance
    //     $lastLoginTime = Carbon::parse($last_login, $manilaTimezone);

    //     // Check if the user is online based on a specific timeframe, e.g., 5 minutes
    //      //Carbon::now()->subSeconds(500)->format('Y-m-d H:i:s');
    //     $lastTime = Carbon::parse($last_login)->format('Y-m-d H:i:s');
    //     $online = strtotime($lastTime);
    //     // Return the result (true if online, false if not)
    //     return $lastTime;
    // }

    private function getCurrentMessage($receiver_id){

        $chatMessage = Message::where(function ($query) use ($receiver_id) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'DESC')->first();

        $recentData = null;

        if($chatMessage) {
            $messageTimestamp = strtotime($chatMessage->created_at);
            $timeDifference = time() - $messageTimestamp;

            $recent_time = '';
            if ($timeDifference < 60) {
                $recent_time = "Just now";
            } else if ($timeDifference < 3600) {
                $minutes = floor($timeDifference / 60);
                $recent_time = "$minutes min ago";
            } else if ($timeDifference < 86400) {
                $hours = floor($timeDifference / 3600);
                $recent_time = "$hours hr ago";
            } else {
                $days = floor($timeDifference / 86400);
                $recent_time = "$days day(s) ago";
            }

            $recentData = [
                'message' => $chatMessage->content,
                'time' => $recent_time,
            ];
        }

        return $recentData;

    }

}

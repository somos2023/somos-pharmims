<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ChatUserResource;



class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request){
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_login = now();
            $user->save();
        }  
        $authType = $request->user()->role_id;

        $type = '';
        if($authType == 2){
            $type = 3;
        } else if($authType == 3){
            $type = 2;
        }

        $contacts = [];

        $chatData = User:: //with(['chat'])
             where('role_id', '=', $type)
            ->where('deleted_flag', '!=', 1)
            ->orderBy('first_name')
            ->get();

         return response([
            'users' => ChatUserResource::collection($chatData),
        ]);
    }


    public function getData(Request $request,string $id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_login = now();
            $user->save();
        }
        
        $contactList = $this->load_contacts($request->user());
        $recentList = $this->recent_chats($request->user());
        $currentMessage = [];
        $currentReceiver = [];
        $chatData = false;


        if($recentList && $id == 0){
            $chatData = Chat::where('sender_id', $request->user()->id)->orderBy('created_at', 'DESC')->first();    
        } else if($recentList && $id != 0){
            $chatData = Chat::where('sender_id', $request->user()->id)
            ->where('receiver_id', $id)
            ->first();
        } 
        

        if($chatData){
            $currentMessage = $this->loadMessages($chatData->unique_id,$chatData->sender_id);
            $currentReceiver = $this->getSpecificUser($chatData->receiver_id);
        } else {
            $currentReceiver = $this->getSpecificUser($id);
        }
            
        
        

        return response()->json([
            'contacts' => $contactList,
            'recent' => $recentList,
            'message' => $currentMessage,
            'receiver' => $currentReceiver
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string', 
        ]);

        $senderId = $request->user()->id;
        // $receiverId = $request->receiver;

        $chatQue = Chat::where('user_id', '=', $senderId)
            ->where('receiver_id', '=', $receiverId)
            ->first();

        if (!$chatQue) {  
            $uniqueId = $this->generateRandomID($senderId . '-' . $receiverId . '-');  
            $chatData = [
                ['sender_id' => $senderId, 'receiver_id' => $receiverId, 'unique_id' => $uniqueId],
                ['sender_id' => $receiverId, 'receiver_id' => $senderId, 'unique_id' => $uniqueId]
            ];

            foreach ($chatData as $data) {
                $chat = new Chat();
                $chat->fill($data); 
                $save = $chat->save();   
            }
        } 

        $newChat = Chat::where('sender_id', '=', $senderId)
            ->where('receiver_id', '=', $receiverId)
            ->first();

        $newMessage = [
            'chat_id' => $newChat->id,
            'unique_id' => $newChat->unique_id,
            'message' => $data['message'] 
        ];

        $chatMessage = new ChatMessage();
        $chatMessage->fill($newMessage); 
        $newsave = $chatMessage->save();  

        if(!$newsave){
            return response()->json(['message' => ['Submit message failed']], 422);
        }

        event(new ChatMessageEvent($chatMessage, $receiverId));

        $currentMessage = $this->loadMessages($newChat->unique_id, $senderId);

        return response()->json(['message'=> $currentMessage],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $userMe = $request->user()->id;

        $chatData = Chat::where('sender_id', '=', $userMe)
            ->where('receiver_id', '=', $id)
            ->first();

        $receiver = $this->getSpecificUser($id);

        if (!$chatData) {  
            return response()->json([
                'receiver' => $receiver,
                'message' => []], 200);
        }

        $uniqueId = $chatData->unique_id;

        $currentMessage = $this->loadMessages($uniqueId,$userMe);

        return response()->json([
            'receiver' => $receiver,
            'message' => $currentMessage
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        //
    }

    private function load_contacts($user){
        $type = '';
        if($user->role_id == 2){
            $type = 3;
        } else if($user->role_id == 3){
            $type = 2;
        }

        $contacts = [];

        $chatData = User::where('role_id', '=', $type)
            ->where('role_id', '!=', '1')
            ->where('deleted_flag', '!=', 1)
            ->orderBy('first_name', 'ASC')
            ->get();

        foreach($chatData as $contact){
            $user = $this->getSpecificUser($contact->id);
            $contact['online'] = $user->online;
            $contact['name'] = $contact->first_name.' '. $contact->last_name;
            $firstLetter = strtoupper(substr($contact->first_name, 0, 1));
            if(!isset($contacts[$firstLetter])){
                $contacts[$firstLetter] = [];
            }
            $contacts[$firstLetter][] = $contact;
        }

        // Sort contacts array by keys (first letters)
        ksort($contacts);

        return $contacts;
    }


    private function recent_chats($user){
        $chatData = Chat::query()
            ->where('receiver_id', '=', $user->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $recentData = [];

        foreach($chatData as $item){
            $userData = $this->getSpecificUser($item->sender_id);
            $chatMessage = ChatMessage::where('unique_id', $item->unique_id)
                ->orderBy('created_at', 'DESC')
                ->first();

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

                $recentData[] = [
                    'id' => $item->sender_id,
                    'name' => $userData ? $userData->first_name . ' ' . $userData->last_name : '',
                    'image' => $userData->image_url ? URL::to($userData->image_url) : URL::to('defaults/def-avatar.png'),
                    'color' => $userData ? $userData->online_color : 'grey',
                    'message' => $chatMessage->message,
                    'time' => $recent_time,
                    'unique_id' => $item->unique_id
                ];
            }
        }

        return $recentData;
    }

    private function loadMessages($uniqueId, $myID) {
    $chatMessages = ChatMessage::query()
        ->select('chat_messages.id', 'chat_messages.message', 'chat_messages.created_at', 'chats.sender_id', 'users.first_name')
        ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
        ->join('users', 'chats.sender_id', '=', 'users.id')
        ->where('chat_messages.unique_id', '=', $uniqueId)
        ->where('chat_messages.deleted_flag', '=', 0)
        ->orderBy('chat_messages.created_at', 'ASC')
        ->get();

    $formattedMessages = [];

    $today = date('Y-m-d');

    foreach ($chatMessages as $item) {
        $timestamp = strtotime($item->created_at);
        $messageDate = date('Y-m-d', $timestamp);

        $formattedMessages[] = [
            'id' => $item->id,
            'first_name' => $item->first_name,
            'time' => date('h:i', $timestamp),
            'message' => $item->message,
            'position' => $item->sender_id == $myID ? 'right' : '',
            'label' => $messageDate == $today ? 'today' : date('Y-m-d', $timestamp),
        ];
    }

    return $formattedMessages;
}


    private function getSpecificUser($id = '') {
        if (!empty($id)) {
            $time = date("Y-m-d H:i:s");
            $user = User::where('id', $id)->first();

            if ($user) {
                $lastLogin = $user->last_login;
                $online = !empty($lastLogin) && strtotime($lastLogin) > strtotime("-5 seconds");

                $user['online_color'] = $online ? 'success' : 'grey';
                $user['online'] = $online;
                $user['name'] = $user->first_name.' '.$user->last_name;
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    // private function getUser($id=''){
    //     if (!empty($id)) {
    //         $time = date("Y-m-d H:i:s");
    //         $user = User::where('id', $id)->first();

    //         if ($user) {
    //             $lastLogin = $user->last_login;
    //             $online = !empty($lastLogin) && strtotime($lastLogin) > strtotime("-5 seconds");

    //             $user['online_color'] = $online ? 'success' : 'grey';
    //             $user['online'] = $online;
    //             return $user;
    //         } else {
    //             return null;
    //         }
    //     } else {
    //         return null;
    //     }
    // }

    private function generateRandomID($prefix, $length = 10) {
        $randomBytes = random_bytes(ceil($length / 2));
        $randomID = $prefix . substr(bin2hex($randomBytes), 0, $length - strlen('CM'));

        return $randomID;
    }


}

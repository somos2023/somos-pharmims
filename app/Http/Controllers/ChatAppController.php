<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Events\UpdateUserOnline;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatUserResource;
use Carbon\Carbon;

class ChatAppController extends Controller
{
    public function index(Request $request){
        if (Auth::check()) {
            $manilaTimezone = 'Asia/Manila';
            $nowInManila = Carbon::now($manilaTimezone);

            $user = Auth::user();
            $user->last_login = $nowInManila;
            $user->timestamps = false;
            $user->save();
        }
        $authType = $request->user()->role_id;

        $type = '';
        if($authType == 2){
            $type = 3;
        } else if($authType == 3){
            $type = 2;
        }

        $chatData = User::
             where('role_id', '=', $type)
            ->where('deleted_flag', '!=', 1)
            ->orderBy('first_name')
            ->get();

         return response([
            'users' => ChatUserResource::collection($chatData),
        ]);
    }

    // public function updateData(Request $request){
    //     if (Auth::check()) {
    //         $manilaTimezone = 'Asia/Manila';
    //         $nowInManila = Carbon::now($manilaTimezone);

    //         $user = Auth::user();
    //         $user->last_login = $nowInManila;
    //         $user->timestamps = false;
    //         $user->save();
    //     }
    //     $authType = $request->user()->role_id;

    //     $type = '';
    //     if($authType == 2){
    //         $type = 3;
    //     } else if($authType == 3){
    //         $type = 2;
    //     }

    //     $chatData = User::
    //          where('role_id', '=', $type)
    //         ->where('deleted_flag', '!=', 1)
    //         ->orderBy('first_name')
    //         ->get();
    //     $users = ChatUserResource::collection($chatData);

    //     event(new UpdateUserOnline($users));

    //     return response(['data'=> $users]);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required',
        ]);

        // Create and store the new message
        $message = new Message();
        $message->sender_id = Auth::id();
        $message->receiver_id = $request->receiver_id;
        $message->content = $request->content;
        $message->save();

        $sendformattedMessage = $this->formatMessage($message);
        $sendMessage = new ChatMessageResource($sendformattedMessage);
        $sendMessage['position'] = '';

        event(new ChatMessageEvent($sendMessage, $request->receiver_id));

        $formattedMessage = $this->formatMessage($message);
        return response()->json([
            'message' => 'Message sent successfully',
            'data' => new ChatMessageResource($formattedMessage)
        ]);
    }



    private function formatMessage($message){
        $today = date('Y-m-d');
        $user = User::where('id', $message->sender_id)->first();

        $timestamp = strtotime($message->created_at);
        $messageDate = date('Y-m-d', $timestamp);

        $message['time'] = date('h:i', $timestamp);
        $message['position'] = $message->sender_id == Auth::id() ? 'right' : '';
        $message['label'] = $messageDate == $today ? 'today' : date('Y-m-d', $timestamp);
        $message['name'] = $user->first_name;

        return $message;
    }

    public function getMessages(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        // Retrieve messages between the authenticated user and the specified receiver
        $messages = Message::where(function ($query) use ($request) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $request->receiver_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('sender_id', $request->receiver_id)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();


        $today = date('Y-m-d');

        foreach ($messages as $item) {
            $user = User::where('id', $item->sender_id)->first();
            

            $timestamp = strtotime($item->created_at);
            $messageDate = date('Y-m-d', $timestamp);

            $item['time'] = date('h:i', $timestamp);
            $item['position'] = $item->sender_id == Auth::id() ? 'right' : '';
            $item['label'] = $messageDate == $today ? 'today' : date('Y-m-d', $timestamp);
            $item['name'] = $user->first_name;

        }

        return response()->json([
            'messages' => ChatMessageResource::collection($messages) 
        ]);
    }

}



<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\User;
use App\Events\UserOnline;
use App\Models\Message;

class ChatController extends Controller
{

    public function chat()
    {
        $users = User::where('id','<>',Auth::id())->get();
        $messages = Message::with('user')->get();
        return view('chat.chatpublic', compact('users','messages'));

    }

    public function sendMessage(Request $req)
    {
        $validated = $req->validate([
            'message' => 'required|string|max:255',
        ]);

        $message = new Message();
        $message->user_id = Auth::id();
        $message->message = $req->message;
        $message->save();

        broadcast(new UserOnline(Auth::user(), $req->message))->toOthers();
        return json_encode([
            'success' => 'done'
        ]);
    }

}

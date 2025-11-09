<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'room_id'   => 'required',
            'from_user' => 'required',
            'message'   => 'required',
        ]);

        // Store message in DB
        $msg = Message::create([
            'room_id' => $request->room_id,
            'from_user' => $request->from_user,
            'message' => $request->message,
        ]);

        // Send to Node.js for real-time broadcast
        Http::post('http://127.0.0.1:6001/broadcast', [
            'roomId' => $request->room_id,
            'message' => $request->message,
            'fromUser' => $request->from_user,
        ]);

        return response()->json(['success' => true, 'data' => $msg]);
    }

    public function getMessages($roomId)
    {
        return Message::where('room_id', $roomId)->orderBy('created_at')->get();
    }
}

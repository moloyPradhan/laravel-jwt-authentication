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

    public function getMessages(Request $request, $roomId)
    {
        $user = $request->user();
        $user_uid = $user->uid;

        $messages = Message::where('room_id', $roomId)
            ->orderBy('created_at')
            ->get()
            ->map(function ($msg) use ($user_uid) {
                return [
                    'id'       => $msg->id,
                    'message'  => $msg->message,
                    'type'     => $msg->from_user == $user_uid ? 'sent' : 'received',
                    'time'     => $msg->created_at->format('Y-m-d H:i:s'),
                    'date'     => $msg->created_at->format('d M Y'),
                ];
            });

        $res = [
            'success' => true,
            'httpStatus' => 200,
            'message' => 'Messages',
            'data'    => ['messages' => $messages],
        ];

        return response()->json($res);
    }
}

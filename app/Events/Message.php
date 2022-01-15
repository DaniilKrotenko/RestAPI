<?php

namespace App\Events;

use App\Notification;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


//    public $username_to;
//    public $username_from;
    public $message;
    public $username;

    public function __construct($message, $username)
    {
        $this->username = $username;
//        $this->username_to = $username_to;
//        $this->username_from = $username_from;
        $this->message = $message;
//        $date = Carbon::now();

//        Notification::create([
//            'from_user' => $username_from,
//            'to_user' =>  $username_to,
//            'text' => $message,
//            'type' => 'message',
//            'date' => $date
//        ]);

    }

    public function broadcastOn()
    {
        return new Channel('chat');
    }

    public function broadcastAs()
    {
        return 'message';
    }
}

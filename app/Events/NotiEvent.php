<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotiEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public function __construct($message)
    {
        $this->message = $message;
        // Format Like This
        // $data = (object) [
        //     'user_id' => $noti->user_id,
        //     'body' => Auth::user()->name . ' commented on your post ' . $post->name . '.',
        // ];
    }

    public function broadcastOn()
    {
        return new Channel('noti-channel');
    }

    public function broadcastAs()
    {
        return 'noti-user';
    }
}

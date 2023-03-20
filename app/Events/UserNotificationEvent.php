<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
class UserNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user_id, $model_id, $model_type,$description,$notification_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $model_id, $model_type, $description = null,$notification_id)
    {
        // dd($model_type);
        $this->user_id = $user_id;
        $this->model_id = $model_id;
        $this->model_type = $model_type;
        $this->description = $description;
        $this->notification_id = $notification_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

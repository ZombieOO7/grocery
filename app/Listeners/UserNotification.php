<?php

namespace App\Listeners;

use App\Events\UserNotificationEvent;
use App\Models\UserNotification as ModelsUserNotification;

class UserNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  ModelsUserNotification  $event
     * @return void
     */
    public function handle(UserNotificationEvent $event)
    {
        ModelsUserNotification::create([
            'user_id' => $event->user_id,
            'model_id' => $event->model_id,
            'model_type' => $event->model_type,
            'description' => $event->description,
            'notification_id' => $event->notification_id,
        ]);
    }
}

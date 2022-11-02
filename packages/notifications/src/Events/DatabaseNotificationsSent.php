<?php

namespace Filament\Notifications\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DatabaseNotificationsSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Model | Authenticatable $user;

    public function __construct(Model | Authenticatable $user)
    {
        $this->user = $user;
    }

    public function broadcastOn(): string
    {
        if (method_exists($this->user, 'receivesBroadcastNotificationsOn')) {
            return new PrivateChannel($this->user->receivesBroadcastNotificationsOn());
        }

        $userClass = str_replace('\\', '.', $this->user::class);

        return new PrivateChannel("{$userClass}.{$this->user->getKey()}");
    }

    public function broadcastAs(): string
    {
        return 'database-notifications.sent';
    }
}

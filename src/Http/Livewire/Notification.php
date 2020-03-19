<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Notification extends Component
{
    public $type = 'info';
    public $message;
    public $notificationVisible = false;

    protected $listeners = [
        'notification.close' => 'close',
        'notification.notify' => 'notify',
    ];

    public function close()
    {
        $this->notificationVisible = false;
    }

    public function notify(array $data)
    {
        $notification = collect($data);
        $this->type = $notification->get('type', $this->type);
        $this->message = $notification->get('message');
        $this->notificationVisible = true;
    }

    public function render()
    {
        return view('filament::livewire.notification');
    }
}
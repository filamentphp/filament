<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Notification extends Component
{
    public $type = 'info';
    public $message;
    public $isOpen = false;

    protected $listeners = [
        'filament.notification.close' => 'close',
        'filament.notification.notify' => 'notify',
    ];

    public function close()
    {
        $this->isOpen = false;
    }

    public function notify(array $data)
    {
        $notification = collect($data);
        $this->type = $notification->get('type', $this->type);
        $this->message = $notification->get('message');
        $this->isOpen = true;
    }

    public function render()
    {
        return view('filament::livewire.notification');
    }
}
<?php

namespace Filament\Notifications;

use Illuminate\Support\Collection as BaseCollection;
use Livewire\Wireable;

class Collection extends BaseCollection implements Wireable
{
    final public function __construct($items = [])
    {
        parent::__construct($items);
    }

    public function toLivewire(): array
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        return (new static($value))->transform(
            fn (array $notification): Notification => Notification::fromArray($notification),
        );
    }
}

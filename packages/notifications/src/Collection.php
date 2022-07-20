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
        return $this
            ->map(fn (Notification $notification): array => $notification->toLivewire())
            ->toArray();
    }

    public static function fromLivewire($value): static
    {
        $static = new static();
        $static->items = $value;
        $static->transform(fn (array $notification): Notification => Notification::fromLivewire($notification));

        return $static;
    }
}
